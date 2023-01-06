<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';?>

<form class="filter">
    <h1>Фильтр</h1>
    <div class="filter__item">
        <h5 class="filter__title">Рост</h5>
        <div class="filter__input-wrap">
            <label for="HEIGHT_MIN">От</label>
            <input type="number" class="filter__input" name="HEIGHT_MIN">
        </div>
        <div class="filter__input-wrap">
            <label for="HEIGHT_MAX">До</label>
            <input type="number" class="filter__input" name="HEIGHT_MAX">
        </div>
        <!-- /.filter__input-wrap -->
    </div>
    <!-- /.filter__item -->
    <br />
    <div class="filter__item">
        <h5 class="filter__title">Дата рождения</h5>
        <div class="filter__input-wrap">
            <label for="BIRTHDATE_MIN">От</label>
            <input type="date" class="filter__input" name="BIRTHDATE_MIN">
        </div>
        <div class="filter__input-wrap">
            <label for="BIRTHDATE_MAX">До</label>
            <input type="date" class="filter__input" name="BIRTHDATE_MAX">
        </div>
        <!-- /.filter__input-wrap -->
    </div>
    <!-- /.filter__item -->
    <br />
    <div class="filter__item">
        <h5 class="filter__title">Название</h5>
        <div class="filter__input-wrap">
            <input type="text" class="filter__input" name="NAME">
        </div>
    </div>
    <!-- /.filter__item -->
    <br />
    <button type="submit">Применить</button>
</form>
<!-- /.filter -->

<br /><br />

<?php

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$arQuery = [];
foreach ($request->getQueryList() as $key => $value) {
    $arQuery[trim(strip_tags($key))] = trim(strip_tags($value));
}

$requestPage = (int) $arQuery['PAGE'];

$minBirthdate = $arQuery['BIRTHDATE_MIN'];
$maxBirthdate = $arQuery['BIRTHDATE_MAX'];
$minHeight = $arQuery['HEIGHT_MIN'];
$maxHeight = $arQuery['HEIGHT_MAX'];
$name = $arQuery['NAME'];

$filter = [];

if (is_numeric($minHeight)) {
    $filter['>HEIGHT_'] = (int) $minHeight;
}

if (is_numeric($maxHeight)) {
    $filter['<HEIGHT_'] = (int) $maxHeight;
}

if ($minBirthdate) {
    try {
        $tsMinBirthdate = strtotime($minBirthdate);
        $oMinBirthdate = \Bitrix\Main\Type\DateTime::createFromTimestamp($tsMinBirthdate);
        $filter['>BIRTHDATE_'] = $oMinBirthdate->format('Y-m-d');
    } catch (\Bitrix\Main\ObjectException $e) {
        die('Неверный формат даты рождения (минимальный)');
    }
}

if ($maxBirthdate) {
    try {
        $tsMaxBirthdate = strtotime($maxBirthdate);
        $oMaxBirthdate = \Bitrix\Main\Type\DateTime::createFromTimestamp($tsMaxBirthdate);
        $filter['<BIRTHDATE_'] = $oMaxBirthdate->format('Y-m-d');
    } catch (\Bitrix\Main\ObjectException $e) {
        die('Неверный формат даты рождения (максимальный)');
    }
}

if ($name) {
    $filter['%NAME'] = $name;
}

$nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
$nav->allowAllRecords(true)->setPageSize(3)->initFromUri();
$nav->setCurrentPage($requestPage + 1);
$listsCount = \Bitrix\Iblock\Elements\ElementTestListTable::getList([
    'filter' => $filter,
    'select' => [
        'NAME',
        'WEIGHT_' => 'WEIGHT.VALUE',
        'HEIGHT_' => 'HEIGHT.VALUE',
        'BIRTHDATE_' => 'BIRTHDATE.VALUE'
    ]
])->getSelectedRowsCount();
$nav->setRecordCount($listsCount);

$arLists = \Bitrix\Iblock\Elements\ElementTestListTable::getList([
    'filter' => $filter,
    'select' => [
        'NAME',
        'WEIGHT_' => 'WEIGHT.VALUE',
        'HEIGHT_' => 'HEIGHT.VALUE',
        'BIRTHDATE_' => 'BIRTHDATE.VALUE'
    ],
    'limit' => $nav->getLimit(),
    'offset' => $nav->getOffset()
])->fetchAll();

foreach ($arLists as $arList) {

    $name = $arList['NAME'];
    $height = $arList['HEIGHT_'];
    $weight = $arList['WEIGHT_'];
    $birthdate = $arList['BIRTHDATE_'];

    echo <<<ITEM
        <div style="border: 1px solid #000; padding: 20px; margin-bottom: 20px;">
            <h1>$name</h1>
            <br />
            <strong>Вес: $weight</strong><br />
            <strong>Рост: $height</strong><br />
            <strong>Дата рождения: $birthdate</strong><br />
        </div>
    ITEM;
}

echo '<div class="nav">';

$strRequest = str_replace('/items_list/', '', $request->getRequestUri());

$pageCount = $listsCount / 3;

for ($i = 0; $i < $pageCount; $i++) {
    echo "<a class='nav__item' href='" . ($strRequest ? $strRequest . "&PAGE=$i" : "?PAGE=$i") . "' style='border: 1px solid #000; padding: 10px; margin: 10px;'>" . ($i + 1) ."</a>";
}

echo "</div>";


require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';