<?php
/**
 * This file is part of the sis-repository
 *
 * Copyright (c) 2015 Vaclav Kraus (krauva@gmail.com)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this
 * source code.
 */

namespace SIS;

use Nette;

class Repository extends Nette\Object
{
	private $server, $user, $password, $port, $sid, $protocol, $charset, $facultyCode;

	public function setServer($server)
	{
		$this->server = $server;
	}

	public function setUser($user)
	{
		$this->user = $user;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function setPort($port)
	{
		$this->port = $port;
	}

	public function setSid($sid)
	{
		$this->sid = $sid;
	}

	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}

	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

	public function setFacultyCode($facultyCode)
	{
		$this->facultyCode = $facultyCode;
	}

	/**
	 * Connect to database.
	 */
	private function connect()
	{
		$ora_host = "(DESCRIPTION =(ADDRESS =(PROTOCOL = " . $this->protocol . ")
					(HOST = " . $this->server . ")
					(PORT = " . $this->port . "))(CONNECT_DATA =(SID = " . $this->sid . ")))";
		$connect = oci_pconnect($this->user, $this->password, $ora_host, $this->charset);

		return $connect;
	}

	/**
	 * Search less information for identification student in search.
	 *
	 * @param $name
	 * @param $surname
	 *
	 * @return array
	 */
	public function searchForStudent($name, $surname)
	{
		$query = "SELECT DISTINCT sident, sprijmeni, sjmeno, inazev
					AS stav, sobor, a.nazev AS obor, AKRSTUPR.nazev
					AS program, druh.nazev AS druh, druh.anazev AS druh_en, srodc
					FROM   stud JOIN osoba ON (sidos=oidos) JOIN inum ON (inum='STAV' AND sstav=ikod )
					JOIN druh ON (sdruh=druh.kod)
					LEFT JOIN ( SELECT kod_fak,stupr,nazev,anazev,forma,obor1,jazyk
					FROM AKRFDOPARAM ) a ON (kod_fak=".$this->facultyCode." AND sstupr=stupr AND sfst=forma
					AND snobor1=obor1 AND svyjazyk=jazyk )
					LEFT JOIN AKRSTUPR ON (a.stupr=AKRSTUPR.kod)
					WHERE sprijmeni = '$surname' AND sjmeno = '$name'
					ORDER BY sprijmeni,sjmeno,srodc,sident";

		$data = $this->execute($query);

		return oci_fetch_assoc($data);
	}

	/**
	 * Return more information about student identified by SIDENT.
	 *
	 * @param $sident
	 *
	 * @return array
	 */
	public function getStudentInfo($sident)
	{
		$query = "SELECT sidos, sprijmeni, sjmeno, ormisto, to_char(odatnar,'DD.MM.YYYY')
                    AS odatnar, druh.nazev AS druh, druh.anazev
                    AS druh_en, a.nazev AS obor, a.anazev AS obor_en, AKRSTUPR.nazev
                    AS program, AKRSTUPR.anazev AS program_en, sstupr, sfst, snobor1, svyjazyk
                    FROM  stud
                    JOIN osoba ON (sidos=oidos)
                    JOIN inum ON (inum='STAV' AND sstav=ikod )
                    JOIN druh ON (sdruh=druh.kod)
                    LEFT JOIN ( SELECT kod_fak,stupr,nazev,anazev,forma,obor1,jazyk FROM AKRFDOPARAM ) a
                    ON (kod_fak=".$this->facultyCode." AND sstupr=stupr AND sfst=forma AND snobor1=obor1 AND svyjazyk=jazyk )
                    LEFT JOIN AKRSTUPR ON (a.stupr=AKRSTUPR.kod)
                    WHERE sident = $sident";
		$data = $this->execute($query);

		return oci_fetch_assoc($data);
	}

	/**
	 * Get information about student's programms and subjects.
	 *
	 * @param $sident
	 *
	 * @return array
	 */
	public function getStudyInfo($sident)
	{
		$query = "SELECT zpovinn, pnazev, panazev, zvysl, zbody, to_char(zdatum,'DD.MM.YYYY')
					AS datum, zskr
					FROM zkous JOIN ( SELECT povinn,pnazev,panazev,vplatiod,vplatido
					FROM povinn UNION SELECT povinn,pnazev,panazev,vplatiod,vplatido
					FROM povinn2 ) ON (zpovinn=povinn AND vplatiod<=zskr AND vplatido>=zskr )
					WHERE  zsplsem='S' AND zident = $sident ORDER BY zskr, zdt";

		$data = $this->execute($query);

		$grades = [];
		while (ocifetch($data)) {
			$grades[] = [
				'code' => ociresult($data, "ZPOVINN"),
				'name' => ociresult($data, "PNAZEV"),
				'name_en' => ociresult($data, "PANAZEV"),
				'grade' => ociresult($data, "ZVYSL"),
				'credits' => ociresult($data, "ZBODY"),
				'date' => ociresult($data, "DATUM"),
				'year' => ociresult($data, "ZSKR")
			];
		}
		return $grades;
	}

	/**
	 * Execute sql query and retur source.
	 *
	 * @param $query sql_text
	 *
	 * @return resource
	 */
	private function execute($query)
	{
		$data = oci_parse($this->connect(), $query);
		oci_execute($data, OCI_DEFAULT);

		return $data;
	}
}