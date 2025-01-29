<?php

namespace VAJOFOWPPGNext\Shared;

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Shared\OrderManager;

class Validator
{
    /**
     * Validates customer name
     * 
     * @param string $name The customer name to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateCustomerName($name)
    {
        $name = trim($name);
        if (empty($name)) {
            return ['isValid' => false, 'message' => esc_html__('Name is required', 'olena-food-ordering')];
        }

        if (strlen($name) < 2 || strlen($name) > 100) {
            return ['isValid' => false, 'message' => esc_html__('Name must be between 2 and 100 characters', 'olena-food-ordering')];
        }

        if (!preg_match('/^[\p{L}\s\'-]+$/u', $name)) {
            return ['isValid' => false, 'message' => esc_html__('Name contains invalid characters', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates email address
     * 
     * @param string $email The email to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateEmail($email)
    {
        $email = trim($email);
        if (empty($email)) {
            return ['isValid' => false, 'message' => esc_html__('Email is required', 'olena-food-ordering')];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['isValid' => false, 'message' => esc_html__('Invalid email format', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates phone number
     * 
     * @param string $phone The phone number to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validatePhone($phone)
    {
        // Remove any whitespace
        $phone = trim($phone);

        // Check if empty before any processing
        if (empty($phone)) {
            return ['isValid' => false, 'message' => esc_html__('Phone number is required', 'olena-food-ordering')];
        }

        // Allow + at start, then remove all non-digits
        if (substr($phone, 0, 1) === '+') {
            $phone = '+' . preg_replace('/[^0-9]/', '', substr($phone, 1));
        } else {
            $phone = preg_replace('/[^0-9]/', '', $phone);
        }

        // Get just the digits for length validation
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return ['isValid' => false, 'message' => esc_html__('Phone number must be between 10 and 15 digits', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates total price
     * 
     * @param float|string $price The price to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validatePrice($price)
    {
        if (!is_numeric($price)) {
            return ['isValid' => false, 'message' => esc_html__('Price must be a number', 'olena-food-ordering')];
        }

        $price = (float) $price;

        if ($price < 0) {
            return ['isValid' => false, 'message' => esc_html__('Price cannot be negative', 'olena-food-ordering')];
        }

        if ($price > 99999.99) {
            return ['isValid' => false, 'message' => esc_html__('Price exceeds maximum allowed amount', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates cart items array
     * 
     * @param array $cartItems The cart items to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateCartItems($cartItems)
    {
        if (!is_array($cartItems) || empty($cartItems)) {
            return ['isValid' => false, 'message' => esc_html__('Cart cannot be empty', 'olena-food-ordering')];
        }

        foreach ($cartItems as $item) {
            if (!isset($item['id']) || !isset($item['quantity']) || !isset($item['price_per_unit'])) {
                return ['isValid' => false, 'message' => esc_html__('Invalid cart item format', 'olena-food-ordering')];
            }

            if ($item['quantity'] < 1) {
                return ['isValid' => false, 'message' => esc_html__('Item quantity must be at least 1', 'olena-food-ordering')];
            }
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates postal/zip code
     * Supports common formats:
     * - US: 12345 or 12345-6789
     * - UK: AA9A 9AA, A9A 9AA, A9 9AA, A99 9AA, AA9 9AA, AA99 9AA
     * - Canada: A9A 9A9
     * - Australia: 9999
     * 
     * @param string $postcode The postal code to validate
     * @param string $country Optional country code (defaults to general validation)
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validatePostcode($postcode, $country = '')
    {
        $postcode = trim($postcode);
        if (empty($postcode)) {
            return ['isValid' => false, 'message' => esc_html__('Postal code is required', 'olena-food-ordering')];
        }

        // Remove all whitespace
        $postcode = preg_replace('/\s+/', '', $postcode);

        switch (strtoupper($country)) {
            case 'US':
                if (!preg_match('/^\d{5}(-\d{4})?$/', $postcode)) {
                    return ['isValid' => false, 'message' => esc_html__('Invalid US ZIP code format', 'olena-food-ordering')];
                }
                break;

            case 'UK':
                if (!preg_match('/^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$/i', $postcode)) {
                    return ['isValid' => false, 'message' => esc_html__('Invalid UK postal code format', 'olena-food-ordering')];
                }
                break;

            case 'CA':
                if (!preg_match('/^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ -]?\d[ABCEGHJ-NPRSTV-Z]\d$/i', $postcode)) {
                    return ['isValid' => false, 'message' => esc_html__('Invalid Canadian postal code format', 'olena-food-ordering')];
                }
                break;

            case 'AU':
                if (!preg_match('/^\d{4}$/', $postcode)) {
                    return ['isValid' => false, 'message' => esc_html__('Invalid Australian postal code format', 'olena-food-ordering')];
                }
                break;

            default:
                // General validation - at least 3 chars, alphanumeric only
                if (!preg_match('/^[A-Z0-9]{3,}$/i', $postcode)) {
                    return ['isValid' => false, 'message' => esc_html__('Invalid postal code format', 'olena-food-ordering')];
                }
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates street address
     * 
     * @param string $address The street address to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateAddress($address)
    {
        $address = trim($address);
        if (empty($address)) {
            return ['isValid' => false, 'message' => esc_html__('Street address is required', 'olena-food-ordering')];
        }

        // Check minimum length (at least 5 characters)
        if (strlen($address) < 5) {
            return ['isValid' => false, 'message' => esc_html__('Address is too short', 'olena-food-ordering')];
        }

        // Check maximum length (reasonable limit of 200 characters)
        if (strlen($address) > 200) {
            return ['isValid' => false, 'message' => esc_html__('Address is too long', 'olena-food-ordering')];
        }

        // Allow letters, numbers, spaces, and common address characters
        if (!preg_match('/^[\p{L}\p{N}\s\',\.\-\/\#\&]+$/u', $address)) {
            return ['isValid' => false, 'message' => esc_html__('Address contains invalid characters', 'olena-food-ordering')];
        }

        // Check for at least one number (most addresses should have a number)
        if (!preg_match('/\d/', $address)) {
            return ['isValid' => false, 'message' => esc_html__('Address should include a street number', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates city name
     * 
     * @param string $city The city name to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateCity($city)
    {
        $city = trim($city);
        if (empty($city)) {
            return ['isValid' => false, 'message' => esc_html__('City is required', 'olena-food-ordering')];
        }

        // Check minimum length (at least 2 characters)
        if (strlen($city) < 2) {
            return ['isValid' => false, 'message' => esc_html__('City name is too short', 'olena-food-ordering')];
        }

        // Check maximum length (reasonable limit of 100 characters)
        if (strlen($city) > 100) {
            return ['isValid' => false, 'message' => esc_html__('City name is too long', 'olena-food-ordering')];
        }

        // Allow letters, spaces, hyphens, and periods (for cities like St. Louis)
        // Allow Unicode letters to support international city names
        if (!preg_match('/^[\p{L}\s\'-\.]+$/u', $city)) {
            return ['isValid' => false, 'message' => esc_html__('City name contains invalid characters', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates order status
     * 
     * @param string $status The order status to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateOrderStatus($status)
    {
        $status = trim($status);
        if (empty($status)) {
            return ['isValid' => false, 'message' => esc_html__('Order status is required', 'olena-food-ordering')];
        }

        $allowedStatuses = OrderManager::getOrderStatuses();
        if (!array_key_exists($status, $allowedStatuses)) {
            return ['isValid' => false, 'message' => esc_html__('Invalid order status', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates delivery status
     * 
     * @param string $status The delivery status to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateDeliveryStatus($status)
    {
        $status = trim($status);
        if (empty($status)) {
            return ['isValid' => false, 'message' => esc_html__('Delivery status is required', 'olena-food-ordering')];
        }

        $allowedStatuses = OrderManager::getDeliveryStatuses();
        if (!array_key_exists($status, $allowedStatuses)) {
            return ['isValid' => false, 'message' => esc_html__('Invalid delivery status', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates delivery method
     * 
     * @param string $method The delivery method to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validateDeliveryMethod($method)
    {
        $method = trim($method);
        if (empty($method)) {
            return ['isValid' => false, 'message' => esc_html__('Delivery method is required', 'olena-food-ordering')];
        }

        $allowedMethods = OrderManager::getDeliveryMethods();
        if (!array_key_exists($method, $allowedMethods)) {
            return ['isValid' => false, 'message' => esc_html__('Invalid delivery method', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }

    /**
     * Validates payment method
     * 
     * @param string $method The payment method to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validatePaymentMethod($method)
    {
        $method = trim($method);
        if (empty($method)) {
            return ['isValid' => false, 'message' => esc_html__('Payment method is required', 'olena-food-ordering')];
        }

        $allowedMethods = OrderManager::getPaymentMethods();
        if (!array_key_exists($method, $allowedMethods)) {
            return ['isValid' => false, 'message' => esc_html__('Invalid payment method', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }


    /**
     * Validates plain text to ensure it contains only English letters, numbers (not first), and underscores
     * 
     * @param string $text The text to validate
     * @return array ['isValid' => bool, 'message' => string]
     */
    public static function validatePlainText($text)
    {
        $text = trim($text);
        if (empty($text)) {
            return ['isValid' => false, 'message' => esc_html__('Text is required', 'olena-food-ordering')];
        }

        // Check if first character is a letter or underscore
        if (!preg_match('/^[a-zA-Z_]/', $text)) {
            return ['isValid' => false, 'message' => esc_html__('Text must start with a letter or underscore', 'olena-food-ordering')];
        }

        // Check entire string contains only allowed characters
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $text)) {
            return ['isValid' => false, 'message' => esc_html__('Text can only contain English letters, numbers, and underscores', 'olena-food-ordering')];
        }

        return ['isValid' => true, 'message' => ''];
    }
}
