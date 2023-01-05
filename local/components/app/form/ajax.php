<?php
/** @global CMain $APPLICATION */

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    return;
}

$APPLICATION->IncludeComponent(
    'app:form',
    '',
    [
        'AJAX' => true,
        'AJAX_DATA' => $request->getPostList()->toArray()
    ],
    false
);