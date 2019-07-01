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

/**
 * Created by Bruno DA SILVA, working for Combodo
 * Date: 04/03/19
 * Time: 17:36
 */

namespace Combodo\iTop\Portal\EventListener;


use Combodo\iTop\Portal\VariableAccessor\CombodoPortalInstanceConf;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ApplicationContextSetUrlMakerClass
{
    /**
     * @var array
     */
    private $aCombodoPortalInstanceConf;

    /**
     * @param string $oCombodoPortalInstanceConf
     */
    public function __construct(CombodoPortalInstanceConf $oCombodoPortalInstanceConf)
    {
        $this->aCombodoPortalInstanceConf = $oCombodoPortalInstanceConf->getVariable();
    }

    public function onKernelRequest(GetResponseEvent $oGetResponseEvent)
    {
        if ($this->aCombodoPortalInstanceConf['properties']['urlmaker_class'] !== null)
        {
            \ApplicationContext::SetUrlMakerClass($this->aCombodoPortalInstanceConf['properties']['urlmaker_class']);
        }
    }
}