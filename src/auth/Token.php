<?php

declare(strict_types=1);

namespace App\Auth;

class Token
{
    /**
     * @var string
     */
    private string $token;

    /**
     * @var DateTime
     */
    private DateTime $expireDate;

    /**
     * @param string   $token
     * @param DateTime $expireDate
     */
    public function __construct(string $token, DateTime $expireDate)
    {
        $this->token = $token;
        $this->expireDate = $expireDate;
    }
}
