<?php
/*
* 2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to team@froggy-commerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author Froggy Commerce <support@froggy-commerce.com>
*  @copyright 2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*  @version   1.0
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/**
 * Class allow to display tpl on the FO
 */
class BWDisplay extends FrontController
{
	// Assign template, on 1.4 create it else assign for 1.5
	public function setTemplate($template)
	{
		if (_PS_VERSION_ >= '1.5')
			parent::setTemplate($template);
		else
			$this->template = $template;
	}

	// Overload displayContent for 1.4
	public function displayContent()
	{
		parent::displayContent();

		echo Context::getContext()->smarty->fetch($this->template);
	}
}