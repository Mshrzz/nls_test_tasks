document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('feedbackForm'),
          submitBtn = form?.querySelector('.form__submit');

    /**
     * Проверяет существование незаполненных полей
     *
     * @returns {boolean}
     */
    const checkFilled = () => {

        const fields = form?.querySelectorAll('input, textarea');

        let fieldEmpty = false;

        fields?.forEach((field) => {
            fieldEmpty = !(field.value || field.textContent);
        });

        return !fieldEmpty;
    };

    /**
     * Проверяет поле Email на корректность если оно есть
     *
     * @returns {boolean}
     */
    const checkEmail = () => {

        const emailField = form?.querySelector('input[type="email"]'),
              emailRegEx = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu;

        return emailField ? emailRegEx.test(emailField.value) : true;
    };

    /**
     * Показывает ошибку при заполнении формы
     *
     * @returns {void}
     */
    const showError = (customMessage = '') => {

        const formError = form?.querySelector('.form__error');

        if (formError) {
            formError.classList.add('form__error_visible');
            formError.textContent = customMessage ? customMessage : 'Не все поля заполнены';
        }
    };

    /**
     * Показывает окно успешной отправки
     *
     * @returns {void}
     */
    const showSuccess = () => {
        const formSuccess = form?.querySelector('.form__success');

        if (formSuccess) {
            formSuccess.classList.add('form__success_visible');
            formSuccess.textContent = 'Заявка отправлена';
        }
    };

    /**
     * Отправляет данные заполненной формы
     *
     * @return {Promise}
     */
    const sendData = async () => {

        const fields = form?.querySelectorAll('input, textarea'),
              data = new FormData();

        fields?.forEach((field) => {
            data.append(field.name, field.value || field.textContent);
        });

        return fetch('/local/components/app/form/ajax.php', {
            method: 'POST',
            body: data
        })
        .then((rs) => rs.json());
    };

    /**
     * Сбрасывает ошибку при заполнении формы
     *
     * @returns {void}
     */
    const resetError = () => {

        const formError = form?.querySelector('.form__error');

        if (formError) {
            formError.classList.remove('form__error_visible');
            formError.textContent = '';
        }
    };

    /**
     * Сбрасывает ошибку при заполнении формы
     *
     * @returns {void}
     */
    const resetSuccess = () => {

        const formSuccess = form?.querySelector('.form__success');

        if (formSuccess) {
            formSuccess.classList.remove('form__success_visible');
            formSuccess.textContent = '';
        }
    };

    /**
     * Обрабатывает отправку формы
     */
    submitBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        resetError();
        resetSuccess();

        const isFilled = checkFilled(),
              emailValid = checkEmail();

        if (isFilled && emailValid) {
            const response = await sendData();

            if (!!response.status) {
                showSuccess();
            } else {
                showError('Произошла внутренняя ошибка. Повторите попытку позже');
            }

        } else {
            showError();
        }
    });
});