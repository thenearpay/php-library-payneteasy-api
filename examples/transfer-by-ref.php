<?php

use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions#Money_transfer_request_parameters
 * @see \PaynetEasy\Paynet\Query\TransferByRefQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array
(
    'client_orderid'            => 'CLIENT-112244',
    'amount'                    =>  9.99,
    'currency'                  => 'USD',
    'ipaddress'                 => '127.0.0.1',
));

/**
 * Для этого запроса необходимо передать данные кредитных карт,
 * между которыми будет происходить перевод средств
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions#Money_transfer_request_parameters
 * @see \PaynetEasy\Paynet\Query\TransferByRefQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\RecurrentCard
 */
$order->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => 8058, 'cvv2' => 123)));
$order->setRecurrentCardTo(new RecurrentCard(array('cardrefid' => 8059)));

/**
 * Создадим обработчик платежей и передадим ему URL для доступа к платежному шлюзу
 *
 * @see \PaynetEasy\Paynet\Transport\GatewayClient::$gatewayUrl
 */
$orderProcessor = new OrderProcessor('https://qa.clubber.me/paynet/api/v2/');

/**
 * Назначим обработчики для разных событий, происходящих при обработке платежа
 *
 * @see ./common/functions.php
 * @see PaynetEasy\Paynet\OrderProcessor::executeWorkflow()
 * @see PaynetEasy\Paynet\OrderProcessor::callHandler()
 */
$orderProcessor->setHandlers(array
(
    OrderProcessor::HANDLER_SAVE_ORDER          => $saveOrder,
    OrderProcessor::HANDLER_STATUS_UPDATE       => $displayWaitPage,
    OrderProcessor::HANDLER_SHOW_HTML           => $displayResponseHtml,
    OrderProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedOrder
));

/**
 * Каждый вызов этого метода выполняет определенный запрос к API Paynet,
 * выбор запроса зависит от этапа обработки заказа
 *
 * @see \PaynetEasy\Paynet\OrderData\Order::$transportStage
 * @see \PaynetEasy\Paynet\OrderProcessor::executeWorkflow()
 * @see \PaynetEasy\Paynet\Workflow\AbstractWorkflow::processOrder()
 */
$orderProcessor->executeWorkflow('transfer-by-ref', $getConfig(), $order, $_REQUEST);
