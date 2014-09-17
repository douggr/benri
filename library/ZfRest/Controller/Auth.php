<?php
/*
 * base/zf-rest
 *
 * @link https://svn.locness.com.br/svn/base/trunk/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
trait ZfRest_Controller_Auth
{
    /**
     * @var stdObject
     */
    static private $user;

    /**
     * @var stdObject
     */
    static private $auth;

    /**
     * @var integer
     */
    private $context;

    /**
     * {@inheritdoc}
     */
    final public function getCurrentUser()
    {
        if ($this->hasAuth() && null === self::$user) {
            self::$user = ZfRest_Model_User::loadWithPermissions(self::$auth, $this->getContext());
        }

        return self::$user;
    }

    /**
     * {@inheritdoc}
     */
    final public function isCurrentUser($id)
    {
        return $this->hasAuth() && $this->getCurrentUser()->id == $id;
    }

    /**
     * {@inheritdoc}
     */
    final public function hasAuth()
    {
        return null !== self::$auth;
    }

    /**
     * {@inheritdoc}
     */
    final public function isAdmin()
    {
        return $this->hasAuth() && $this->getCurrentUser()->admin;
    }

    /**
     * {@inheritdoc}
     */
    final public function isSiteAdmin()
    {
        $user    = $this->getCurrentUser();
        $context = $this->getContext();

        return !!($user && $user->isSiteAdmin());
    }

    /**
     * {@inheritdoc}
     */
    final public function setAuth($authorizationHeader)
    {
        $auth = explode(' ', $authorizationHeader);

        if (2 === sizeof($auth) && 'Bearer' === $auth[0]) {
            self::$auth = $auth[1];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    final public function getContext()
    {
        return $this->context;
    }
}
