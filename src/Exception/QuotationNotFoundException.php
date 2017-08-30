<?php
namespace Axado\Exception;

/**
 * Exception to throw when Axado request do not returns a quotation for the
 * given CEP
 */
class QuotationNotFoundException extends ShippingException
{
}
