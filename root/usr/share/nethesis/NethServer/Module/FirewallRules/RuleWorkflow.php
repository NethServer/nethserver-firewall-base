<?php

namespace NethServer\Module\FirewallRules;

/*
 * Copyright (C) 2014  Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * TODO: add component description here
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class RuleWorkflow
{
    private $state;

    const SESSION_KEY = __CLASS__;

    public function __construct()
    {
        $this->state = new \ArrayObject();
    }

    /**
     *
     * @param \Nethgui\Utility\SessionInterface $session
     * @param string $path
     * @param string $ruleId
     * @return \NethServer\Module\FirewallRules\RuleWorkflow
     */
    public function start(\Nethgui\Utility\SessionInterface $session, $startId, $path, $ruleId, $defaults = array())
    {
        $session->store(self::SESSION_KEY, $this->state);
        $this->state->exchangeArray($defaults);
        $this->state['ruleId'] = $ruleId;
        $this->state['startId'] = $startId;
        $this->state['backPath'] = $path;
        return $this;
    }

    public function getStartIdentifier()
    {
        return $this->state['startId'];
    }

    /**
     *
     * @param \Nethgui\Utility\SessionInterface $session
     * @return \NethServer\Module\FirewallRules\RuleWorkflow
     * @throws \RuntimeException
     */
    public function resume(\Nethgui\Utility\SessionInterface $session)
    {
        $this->state = $session->retrieve(self::SESSION_KEY);
        if ( ! $this->state instanceof \ArrayObject) {
            throw new \RuntimeException("NULL workflow state", 1401978436);
        }
        return $this;
    }

    /**
     *
     * @param string $field
     * @return \NethServer\Module\FirewallRules\RuleWorkflow
     */
    public function focus($field)
    {
        $this->state['field'] = $field;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return \NethServer\Module\FirewallRules\RuleWorkflow
     * @throws \LogicException
     */
    public function assign($value)
    {
        if( ! $this->state['field'] ) {
            throw new \LogicException("Workflow must focus on a field before assigning a value", 1402040802);
        }
        $this->state[$this->state['field']] = $value;
        return $this;
    }

    public function getReturnPath()
    {
        return sprintf('%s?f=%s&h=%s', $this->state['backPath'], $this->state['field'], substr(md5(\serialize($this->state)), 0, 5));
    }

    public function getRuleId()
    {
        return $this->state['ruleId'];
    }

    /**
     *
     * @param \ArrayAccess $o
     * @param array $keys
     * @return \NethServer\Module\FirewallRules\RuleWorkflow
     */
    public function copyTo(\ArrayAccess $o, $keys = array())
    {
        foreach ($keys as $k) {
            if ( ! isset($this->state[$k])) {
                continue;
            }
            $o[$k] = $this->state[$k];
        }
        return $this;
    }

}