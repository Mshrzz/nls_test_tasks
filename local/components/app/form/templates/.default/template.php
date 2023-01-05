<?php
/** @var $arResult */
?>

<?php if (!empty($arResult['QUESTIONS'])):?>
    <form class="form form_feedback" id="feedbackForm">
        <span class="form__error"></span>
        <!-- /.form__error -->
        <span class="form__success"></span>
        <!-- /.form__success -->
        <h1 class="title form__title">Форма обратной связи</h1>
        <!-- /.title form__title -->
        <div class="form__content">
            <?php foreach ($arResult['QUESTIONS'] as $arQuestion):?>
                <div class="form__input-wrap">
                    <label for="<?=$arQuestion['CODE']?>"><?=$arQuestion['NAME']?></label>
                    <?php if ((bool) $arQuestion['USER_TYPE']):?>
                        <textarea name="<?=$arQuestion['CODE']?>" id="" cols="30" rows="10" class="form__input form__input_large" required></textarea>
                    <?php else:?>
                        <input type="<?=$arQuestion['CODE'] === 'EMAIL' ? 'email' : 'text'?>" class="form__input" name="<?=$arQuestion['CODE']?>" required>
                        <!-- /.form__input -->
                    <?php endif;?>
                </div>
                <!-- /.form__input-wrap -->
            <?php endforeach;?>
            <button class="form__submit">Отправить</button>
            <!-- /.form__submit -->
        </div>
        <!-- /.form__content -->
    </form>
    <!-- /.form form_feedback -->
<?php endif;?>