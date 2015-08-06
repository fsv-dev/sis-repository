<?php

/**
 * This file is part of the sis-repository
 *
 * Copyright (c) 2015 Vaclav Kraus (krauva@gmail.com)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this
 * source code.
 */
namespace SIS\DI;

use Nette;

class SISExtension extends Nette\DI\CompilerExtension
{
	private $default = [
		'protocol' => 'TCP',
	];

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->default);

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('sis-repository'))
			->setClass('SIS\Repository')
			->addSetup('setServer', array($config['server']))
			->addSetup('setUser', array($config['user']))
			->addSetup('setPassword', array($config['password']))
			->addSetup('setPort', array($config['port']))
			->addSetup('setSid', array($config['sid']))
			->addSetup('setProtocol', array($config['protocol']))
			->setInject(FALSE);
	}
}