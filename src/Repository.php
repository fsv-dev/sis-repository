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
	private $server, $user, $password, $port, $sid, $protocol, $db;

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

	/**
	 * Connect to database.
	 */
	public function connect()
	{
		$ora_host = "(DESCRIPTION =(ADDRESS =(PROTOCOL = ".$this->protocol.")
					(HOST = ".$this->server.")
					(PORT = ".$this->port."))(CONNECT_DATA =(SID = ".$this->sid.")))";
		$connect = ocilogon($this->user, $this->password, $ora_host);
		$this->db = $connect;
	}
}