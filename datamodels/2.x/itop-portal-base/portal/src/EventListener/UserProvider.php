<?php
/**
 * Copyright (C) 2013-2019 Combodo SARL
 *
 * This file is part of iTop.
 *
 * iTop is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * iTop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 *
 *
 */

namespace Combodo\iTop\Portal\EventListener;

use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Dict;
use LoginWebPage;
use UserRights;
use ModuleDesign;

/**
 * Class UserProvider
 *
 * @package Combodo\iTop\Portal\EventListener
 * @since 2.7.0
 */
class UserProvider implements ContainerAwareInterface
{
    /** @var \ModuleDesign $oModuleDesign */
    private $oModuleDesign;
    /** @var string $sPortalId */
	private $sPortalId;
	/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
	private $oContainer;

	/**
	 * UserProvider constructor.
	 *
	 * @param \ModuleDesign $oModuleDesign
	 * @param string        $sPortalId
	 */
    public function __construct(ModuleDesign $oModuleDesign, $sPortalId)
    {
        $this->oModuleDesign = $oModuleDesign;
	    $this->sPortalId = $sPortalId;
    }

	/**
	 * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $oGetResponseEvent
	 *
	 * @throws \Exception
	 */
    public function onKernelRequest(GetResponseEvent $oGetResponseEvent)
    {
        // User pre-checks
        // Note: At this point the Exception handler is not registered, so we can't use $oApp::abort() method, hence the die().
        // - Checking user rights and prompt if needed (401 HTTP code returned if XHR request)
        $iExitMethod = ($oGetResponseEvent->getRequest()->isXmlHttpRequest()) ? LoginWebPage::EXIT_RETURN : LoginWebPage::EXIT_PROMPT;
        $iLogonRes = LoginWebPage::DoLoginEx($this->sPortalId, false, $iExitMethod);
        if( ($iExitMethod === LoginWebPage::EXIT_RETURN) && ($iLogonRes != 0) )
        {
            die(Dict::S('Portal:ErrorUserLoggedOut'));
        }
        // - User must be associated with a Contact
        if (UserRights::GetContactId() == 0)
        {
            die(Dict::S('Portal:ErrorNoContactForThisUser'));
        }

        // User
        $oUser = UserRights::GetUserObject();
        if ($oUser === null)
        {
            throw new Exception('Could not load connected user.');
        }
        $this->oContainer->set('combodo.current_user', $oUser);
    }

	/**
	 * Sets the container.
	 *
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface|null $oContainer
	 */
	public function setContainer(ContainerInterface $oContainer = null)
	{
		$this->oContainer = $oContainer;
	}
}