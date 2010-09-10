<?php

/**
 * A basic interface which is open to all kind of authentication methods.
 */
interface IAuthenticatable {
    /**
     * Sets the credentials of the authenticatable module with the given array.
     * @param array(mixed) $credentials The credentials as array.
     * @return void
     */
    function SetCredentials($credentials);

    /**
     * Checks the given credentials and returns an boolean value indicating
     * wether the authentication was successful or not.
     * @return boolean
     */
    function Authenticate();
}

?>
