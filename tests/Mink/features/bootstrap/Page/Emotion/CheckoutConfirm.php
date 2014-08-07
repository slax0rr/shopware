<?php
namespace Emotion;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CheckoutConfirm extends Page
{
    /**
     * @var string $path
     */
    protected $path = '/checkout/confirm';

    public $cssLocator = array(
        'pageIdentifier'  => 'div#confirm',
        'deliveryForm' => 'form.payment',
        'proceedCheckoutForm' => 'div.additional_footer > form'
    );

    public function verifyPage()
    {
        $locators = array('pageIdentifier');
        $elements = \Helper::findElements($this, $locators, $this->cssLocator, false, false);

        if (!empty($elements['pageIdentifier'])) {
            return;
        }

        $message = array('You are not on CheckoutConfirm page!', 'Current URL: '.$this->getSession()->getCurrentUrl());
        \Helper::throwException($message);
    }

    /**
     * Login a user
     * @param string $email
     * @param string $password
     */
    public function login($email, $password)
    {
        $this->open();

        $this->fillField('email', $email);
        $this->fillField('password', $password);

        $this->pressButton('Anmelden');
    }

    /**
     * Changes the billing address
     * @param array $values
     */
    public function changeBilling($values)
    {
        $this->open();

        $button = $this->find('css', 'div.invoice-address a.button-middle:nth-of-type(1)');
        $button->click();

        $this->getPage('Account')->changeBilling($values);
    }

    /**
     * Changes the shipping address
     * @param array $values
     */
    public function changeShipping($values)
    {
        $this->open();

        $button = $this->find('css', 'div.shipping-address a.button-middle:nth-of-type(1)');
        $button->click();

        $this->getPage('Account')->changeShipping($values);
    }

    /**
     * Changes the payment method
     * @param integer $value
     * @param array   $data
     */
    public function changePayment($value, $data = array())
    {
        $this->open();

        $button = $this->find('css', 'div.payment-display a.button-middle');
        $button->click();

        $this->selectFieldOption('register[payment]', $value);

        if ($value === 2) {
            foreach ($data as $field => $value) {
                $this->fillField($field, $value);
            }
        }

        $this->pressButton('Ändern');
    }

    /**
     * Changes the Dispatch method
     * @param integer $value
     */
    public function changeDelivery($value)
    {
        $this->open();

        $this->selectFieldOption('sDispatch', $value);

        $button = $this->find('css', 'div.dispatch-methods input.button-middle');
        $button->press();
    }

    public function getOrderNumber()
    {
        $orderDetails = $this->find('css', 'div#finished div.orderdetails')->getText();

        preg_match("/\d+/",$orderDetails,$orderNumber);
        $orderNumber = intval($orderNumber[0]);

        return $orderNumber;
    }
}