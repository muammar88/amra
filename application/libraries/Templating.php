<?php

/**
 *  -----------------------
 *	Templating library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Templating
{

	private $auth;

	function __construct()
	{
		$this->templating = &get_instance();
		/*
		|-----------------------------------
		|	Define rooting
		|-----------------------------------
		*/
		$this->templating->auth  			= 'Public/Authentication/Content';
	}

	/**
	 *	Sign Up Templating
	 *	@return array
	 */
	function sign_up_templating($data)
	{
		$parser = $this->templating->parser->parse('Public/Public_header', $data);
		$parser = $this->templating->parser->parse('Sign_up/Sign_up_content', $data);
		$parser = $this->templating->parser->parse('Public/Public_footer', $data);
		return $parser;
	}

	/**
	 *	Sign In Templating
	 *	@return array
	 */
	function sign_in_templating($data)
	{
		$parser = $this->templating->parser->parse('Public/Public_header', $data);
		$parser = $this->templating->parser->parse('Sign_in/Sign_in_content', $data);
		$parser = $this->templating->parser->parse('Public/Public_footer', $data);
		return $parser;
	}

	/**
	 *	Users Templating
	 *	@return array
	 */
	function users_templating($data)
	{
		$parser = $this->templating->parser->parse('User/Users_header', $data);
		$parser = $this->templating->parser->parse('User/Users_content', $data);
		$parser = $this->templating->parser->parse('User/Users_footer', $data);
		return $parser;
	}

	/**
	 *	Error Templating
	 *	@return array
	 */
	function error_templating($data)
	{
		$parser = $this->templating->parser->parse('Error/Error_header', $data);
		$parser = $this->templating->parser->parse('Error/Error_content', $data);
		$parser = $this->templating->parser->parse('Error/Error_footer', $data);
		return $parser;
	}

	function landing_templating($data)
	{
		$parser = $this->templating->parser->parse('Landing_pages/Header', $data);
		$parser = $this->templating->parser->parse('Landing_pages/Content', $data);
		$parser = $this->templating->parser->parse('Landing_pages/Footer', $data);
		return $parser;
	}

	function payment_templating($data)
	{
		$parser = $this->templating->parser->parse('Landing_pages/Header', $data);
		$parser = $this->templating->parser->parse('Public/Payment', $data);
		$parser = $this->templating->parser->parse('Landing_pages/Footer', $data);
		return $parser;
	}

	function renew_templating($data)
	{
		$parser = $this->templating->parser->parse('Public/Public_header', $data);
		$parser = $this->templating->parser->parse('Public/Renew', $data);
		$parser = $this->templating->parser->parse('Public/Public_footer', $data);
		return $parser;
	}


	function term_condition_templating($data){
		$parser = $this->templating->parser->parse('Public/Public_header', $data);
		$parser = $this->templating->parser->parse('Public/Term_condition/Content', $data);
		$parser = $this->templating->parser->parse('Public/Public_footer', $data);
		return $parser;
	}


	function superman_templating($data){
		$parser = $this->templating->parser->parse('Header', $data);
		$parser = $this->templating->parser->parse('Content', $data);
		$parser = $this->templating->parser->parse('Footer', $data);
		return $parser;
	}

	function superman_sign_in_templating($data){
		$parser = $this->templating->parser->parse('Sign_in/Header', $data);
		$parser = $this->templating->parser->parse('Sign_in/Content', $data);
		$parser = $this->templating->parser->parse('Sign_in/Footer', $data);
		return $parser;
	}

}
