<?php

namespace NovaExternalLogin;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\ResourceTool;

class ExternalLogin extends ResourceTool
{
    protected string $uniqueKey = 'external-login';

    protected ?string $buttonTitle = null;

    protected ?\Closure $loginUserCallback = null;

    /**
     * @return string
     */
    public function uniqueKey(): string
    {
        return $this->uniqueKey;
    }

    /**
     * Set button title.
     *
     * @param string $uniqueKey
     *
     * @return $this
     */
    public function setUniqueKey(string $uniqueKey): ExternalLogin
    {
        $this->uniqueKey = $uniqueKey;

        return $this;
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Login as user';
    }

    /**
     * Set button title.
     *
     * @param string|null $buttonTitle
     *
     * @return $this
     */
    public function buttonTitle(?string $buttonTitle = null): ExternalLogin
    {
        $this->buttonTitle = $buttonTitle;

        return $this;
    }

    /**
     * Set specific resource id (by default used current resource id)
     *
     * @param mixed $id
     *
     * @return ExternalLogin
     */
    public function resourceID($id): ExternalLogin
    {
        return $this->withMeta([ 'resourceID' => $id ]);
    }

    /**
     * External dashboard url.
     *
     * @param string $url
     *
     * @return ExternalLogin
     */
    public function postUrl(string $url = 'http://localhost:3000'): ExternalLogin
    {
        return $this->withMeta([ 'postUrl' => rtrim($url, '/') ]);
    }

    /**
     * The path to the page where the generated token will be saved.
     *
     * @param string $url
     *
     * @return ExternalLogin
     */
    public function iframePath(string $url = 'external-login'): ExternalLogin
    {
        return $this->withMeta([ 'iframePath' => '/' . ltrim($url, '/') ]);
    }

    /**
     * The path to the page where user will be redirected after login.
     *
     * @param string $url
     *
     * @return ExternalLogin
     */
    public function redirectPath(string $url = 'login'): ExternalLogin
    {
        return $this->withMeta([ 'redirectPath' => '/' . ltrim($url, '/') ]);
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'nova-external-login';
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'uniqueKey' => $this->uniqueKey,
            'buttonTitle' => $this->buttonTitle ?? $this->name(),
        ]);
    }

    /**
     * Set login callback
     * @param \Closure $callback
     *
     * @return $this
     */
    public function loginUsing(\Closure $callback): ExternalLogin
    {
        $this->loginUserCallback = $callback;

        return $this;
    }


    /**
     * @param NovaRequest $request
     *
     * @return false|mixed|null
     */
    public function loginUser(NovaRequest $request)
    {
        if (is_callable($this->loginUserCallback)) {
            return call_user_func_array($this->loginUserCallback, [ $request, $this ]);
        }

        return null;
    }
}
