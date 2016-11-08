<?php
namespace Ry\Socin\Core\Interfaces;

interface ConnectorInterface
{
	public function hasSession($accessToken = null);
	
	public function setSession($session = null);
	
	public function getSession();
}