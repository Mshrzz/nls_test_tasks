<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @Form
 *
 * Описывает работу компонента обратной связи
 */
class Form extends CBitrixComponent
{
    /** @var int - ID инфоблока с формами */
    protected int $iblockId;

    /**
     * Form constructor
     *
     * @param $data
     */
    public function __construct($data)
    {
        parent::__construct($data);

        try {
            $arIblock = \Bitrix\Iblock\IblockTable::getList([
                'filter' => ['CODE' => 'feedback_forms']
            ])->fetch();

            $this->iblockId = (int) $arIblock['ID'];
        }
        catch (Exception $e) {
            die();
        }
    }

    /**
     * Возвращает вопросы формы
     *
     * @return array
     */
    public function getQuestions(): array
    {
        try {
            $arQuestions = \Bitrix\Iblock\PropertyTable::getList([
                'filter' => ['IBLOCK_ID' => $this->iblockId],
                'select' => ['NAME', 'CODE', 'USER_TYPE', 'IS_REQUIRED']
            ])->fetchAll();
        }
        catch (Exception $e) {
            $arQuestions = [];
        }

        return $arQuestions ?? [];
    }

    /**
     * Создает заполненную заявку в инфоблоке
     *
     * @param array $data - данные заявки
     * @return int - ID созданной заявки
     * @throws Exception - если заявка не создана
     */
    public function createEntry(array $data): int
    {
        $oIblock = new CIBlockElement();
        $entryId = (int) $oIblock->Add([
            'NAME' => 'Новая заполненная форма',
            'IBLOCK_ID' => $this->iblockId,
            'PROPERTY_VALUES' => [
                'EMAIL' => $data['EMAIL'],
                'USER_NAME' => $data['USER_NAME'],
                'DESCRIPTION' => [
                    'VALUE' => [
                        'TEXT' => $data['DESCRIPTION'],
                        'TYPE' => 'text'
                    ]
                ]
            ]
        ]);

        if (!$entryId) {
            throw new Exception($oIblock->LAST_ERROR);
        }

        return $entryId;
    }

    /**
     * Отправляет сообщение на почту
     *
     * @param array $data - данные заполненной формы
     * @return void
     */
    public function sendMail(array $data): void
    {
        Bitrix\Main\Mail\Event::send([
            'EVENT_NAME' => 'FEEDBACK_FORM',
            'MESSAGE_ID' => '52',
            'LID' => 's1',
            'C_FIELDS' => [
                'EMAIL' => $data['EMAIL'],
                'USER_NAME' => $data['USER_NAME'],
                'DESCRIPTION' => $data['DESCRIPTION']
            ]
        ]);
    }

    /**
     * Инициализация компонента
     *
     * @return mixed|void|null
     * @throws JsonException
     */
    public function executeComponent()
    {
        if ($this->arParams['AJAX']) {

            try {
                $this->createEntry($this->arParams['AJAX_DATA']);
                $this->sendMail($this->arParams['AJAX_DATA']);
                echo json_encode(['status' => 1, 'message' => ''], JSON_THROW_ON_ERROR);
                die();
            }
            catch (Exception $e) {
                echo json_encode(['status' => 0, 'message' => $e->getMessage()], JSON_THROW_ON_ERROR);
                die();
            }

        } else {

            $this->arResult['QUESTIONS'] = $this->getQuestions();
            $this->includeComponentTemplate();

        }
    }
}