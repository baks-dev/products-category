/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */


/* определяем язык системы по тегу HTML */
// $htmlLang = document.getElementsByTagName('html');
// const $lang = $htmlLang[0].getAttribute('lang');


/** Коллекция СЕКЦИЙ для свойств продукта*/

/* кнопка Добавить коллекцию */
//var $addButton = document.querySelector('button.add_collection');
let $addButtonSection = document.getElementById('section_addCollection');

/* Блок для новой коллекции */
let $blockCollection = document.getElementById('section_collection');

if ($blockCollection) {
    // /* добавить событие на удаление ко всем существующим элементам формы в блок с классом .del-item */
    // let $delItem = $blockCollection.querySelectorAll('.del-item-section');
    //
    // /* Удаляем при клике колекцию СЕКЦИЙ */
    // $delItem.forEach(function (item) {
    //     item.addEventListener('click', function () {
    //
    //         let $counter = $blockCollection.getElementsByClassName('item-collection-section').length;
    //         if ($counter > 1) {
    //             item.closest('.item-collection-section').remove();
    //         }
    //         else
    //         {
    //             alert('Минимально должна быть добавлена одна секция');
    //         }
    //     });
    // });

    /* добавить событие на удаление ко всем существующим секциям свойств */
    deleteSection($blockCollection);

    /* добавить событие на удаление свойства из секции */
    deleteField($blockCollection)


    /* Существующие кнопки Добавить поле в секцию */
    let $addButtonField = document.querySelectorAll('[id^="createSectionField"]');
    $addButtonField.forEach(function (item) {
        createSectionField(item.dataset.section);
    });


    // /* Удаляем при клике колекцию ПОЛЕЙ */
    // /* добавить событие на удаление ко всем существующим элементам формы в блок с классом .del-item */
    // let $delItemField = $blockCollection.querySelectorAll('.del-item-field');
    //
    // /* Удаляем при клике колекцию ПОЛЕЙ */
    // $delItemField.forEach(function (item) {
    //     item.addEventListener('click', function () {
    //
    //         let $countField = item.closest('.item-collection-field').parentNode.getElementsByClassName('item-collection-field').length;
    //
    //         if ($countField > 1) {
    //             item.closest('.item-collection-field').remove();
    //         }
    //         else
    //         {
    //             alert('Минимально должно быть добавлено одно поле');
    //         }
    //     });
    // });


    /* получаем количество коллекций и присваиваем data-index прототипу */
    //$blockCollection.dataset.sectionIndex = $blockCollection.getElementsByClassName('item-collection-section').length.toString();

    /**
     *
     * Добавить коллекцию СВОЙСТВ ПРОДУКТА
     *
     *
     */

    /* Добавляем новую коллекцию */
    $addButtonSection.addEventListener('click', function () {
        /* получаем прототип коллекции  */
        let newForm = this.dataset.prototype;
        let index = this.dataset.index * 1;


        /* Замена '__name__' в HTML-коде прототипа на
        вместо этого будет число, основанное на том, сколько коллекций */
        newForm = newForm.replace(/__category_section__/g, index);
        //newForm = newForm.replace(/__FIELD_SORT__/g, 100);
        //newForm = newForm.replace(/__FIELDS_INDEX__/g, 1);


        /* Вставляем новую коллекцию */
        let div = document.createElement('div');
        div.id = 'item-collection-section-' + index;

        div.classList.add('card');
        div.classList.add('p-4');
        div.classList.add('mb-3');
        div.classList.add('border-light');
        div.classList.add('item-collection-section');


        div.innerHTML = newForm;
        $blockCollection.append(div);


        /* Плавная прокрутка к элементу */
        div.scrollIntoView({block: "center", inline: "center", behavior: "smooth"});

        // let field = document.getElementById('field-collection-' + index);
        // field.innerHTML = field.innerHTML.replace(/__FIELDS__/g, '0')
        //     .replace(/__FIELD_SORT__/g, '100');


        /* Добавить поле в секцию */
        createSectionField(index);


        /* Удаляем при клике СЕКЦИЮ */
        deleteSection(div);
        /* Удаляем при клике СВОЙСТВО */
        deleteField(div);

        /* Увеличиваем data-index на 1 после вставки новой коллекции */
        this.dataset.index = (index + 1).toString();

    });
}


/** Добавить секцию  */
function deleteSection($block) {

    let $delItem = $block.querySelectorAll('.del-item-section');

    /* Удаляем при клике колекцию СЕКЦИЙ */
    $delItem.forEach(function (item) {
        item.addEventListener('click', function () {

            let $counter = $block.getElementsByClassName('item-collection-section').length;

            if ($counter > 1) {
                item.closest('.item-collection-section').remove();
            } else {
                alert('Минимально должна быть добавлена одна секция');
            }
        });
    });
}

/** Удалить свойство из секции  */
function deleteField($block) {

    $delItem = $block.querySelectorAll('.del-item-field');

    /* Удаляем при клике свойство из секции */
    $delItem.forEach(function (item) {
        item.addEventListener('click', function () {

            let $fieldCollection = document.getElementById('field-collection-' + this.dataset.section);
            let $counter = $fieldCollection.querySelectorAll('.item-collection-field').length;

            if ($counter > 1) {
                item.closest('.item-collection-field').remove();
            } else {
                alert('Минимально должна быть добавлена одна секция');
            }
        });
    });
}


/** Добавить свойство в секцию  */
function createSectionField(section) {

    /* Событие на клик добавления полей в секцию */
    let $btnAddFields = document.getElementById('createSectionField' + section);

    //$btnCreateSectionField = div.querySelector('#createSectionField'+ index);
    $btnAddFields.addEventListener('click', function () {

        //$btnAddFields = document.getElementById('createSectionField'+section)

        let section_id = $btnAddFields.dataset.section;

        /* получаем прототип коллекции  */
        let newForm = $btnAddFields.dataset.prototype;

        let index = $btnAddFields.dataset.index;


        /* Замена '__name__' в HTML-коде прототипа на
                    вместо этого будет число, основанное на том, сколько коллекций */
        newForm = newForm.replace(/__category_section__/g, section_id);
        newForm = newForm.replace(/__section_field__/g, index);
        newForm = newForm.replace(/__FIELD_SORT__/g, index * 100 + 100);


        /* Вставляем новую коллекцию */
        let div = document.createElement('div');
        div.id = 'item-collection-field-' + index;
        div.classList.add('item-collection-field');
        div.classList.add('pb-3');

        div.innerHTML = newForm;

        document.getElementById('field-collection-' + section_id).append(div);


        /* Плавная прокрутка к элементу */
        div.scrollIntoView({block: "center", inline: "center", behavior: "smooth"});

        /* Удаляем при клике СВОЙСТВО */
        deleteField(div);

        $btnAddFields.dataset.index = (index * 1 + 1).toString();

        //div.innerHTML = newForm;
        //$blockCollection.append(div);

    });

}


// function createField($blockCollectionFields, index) {
//     /* получаем прототип коллекции  */
//     let newFormField = $blockCollectionFields.dataset.prototype;
//     let index_field = $blockCollectionFields.dataset.index * 1 + 1;
//
//     /* Замена '__name__' в HTML-коде прототипа на
//     вместо этого будет число, основанное на том, сколько коллекций */
//     newFormField = newFormField.replace(/__SECTION__/g, index);
//     newFormField = newFormField.replace(/__FIELD__/g, index_field);
//
//     /* Вставляем новую коллекцию */
//     let div = document.createElement('div');
//     div.innerHTML = newFormField;
//     $blockCollectionFields.append(div);
//
//     /* Удаляем при клике колекцию */
//     div.querySelector('.del-item-field').addEventListener('click', function () {
//         let $countField = div.parentNode.getElementsByClassName('item-collection-field').length;
//         if ($countField > 1) {  div.remove(); } /* Удаляем блок */
//     });
//
//     /* Увеличиваем data-index на 1 после вставки новой коллекции */
//     $blockCollectionFields.dataset.index = index_field;
// }


document.querySelectorAll('.is-reference').forEach(function (isReference) {

    /* Обрабатываем уже существующие и сохраненные ТП */
    chanfeReferenc(isReference);

    isReference.addEventListener('change', function () {
        chanfeReferenc(this);
    });

});


/** Получаем поле 'Название раздела' по локали для 'Символьный код категории' */
let $name = document.querySelector("input[data-lang='product_category_form_translate_0_" + $lang + "']");

if ($name) {

    setTimeout(function initBootstrap() {

        if (typeof catUrl.debounce === 'function') {

            $name.addEventListener('input', catUrl.debounce(500));
            return;
        }
        setTimeout(initBootstrap, 100);

    }, 100);


    function catUrl() {
        /* Заполняем транслитом URL */
        document.getElementById('product_category_form_info_url').value = translitRuEn(this.value).toLowerCase();
    }
}

/* Торговое предложение с ценой - снимаем чекбокс*/
// let isPrice = document.querySelectorAll('input[id*="_isPrice"]');
//
// isPrice.forEach(function (item) {
//     item.addEventListener('change', function (event) {
//         let check = this.id;
//         document.querySelectorAll('input[id*="_isPrice"]').forEach(function (event) {
//             if(event.id !== check) { event.checked = false; }
//         });
//     });
// });


/** Обрабатываем чекбоксы торговых предложений и вариантов */


$offerSettings = document.getElementById('offer-settings');
$checkboxOfferSettings = document.getElementById('product_category_form_offer_offer');
$nameOfferSettings = document.querySelectorAll("input[id^='product_category_form_offer_translate']");

$variationSettings = document.getElementById('variation-settings');
$checkboxVariationSettings = document.getElementById('product_category_form_offer_variation_variation');
$nameVariationSettings = document.querySelectorAll("input[id^='product_category_form_offer_variation_translate']");


$modificationSettings = document.getElementById('modification-settings');
$checkboxModificationSettings = document.getElementById('product_category_form_offer_variation_modification_modification');
$nameModificationSettings = document.querySelectorAll("input[id^='product_category_form_offer_variation_modification_translate']");


$checkboxOfferSettings.addEventListener('change', function () {

    if (this.checked == false) {
        $offerSettings.classList.add('d-none');

        $checkboxVariationSettings.checked = false;
        $checkboxVariationSettings.disabled = true;

        $checkboxModificationSettings.checked = false;
        $checkboxModificationSettings.disabled = true;


        $variationSettings.classList.add('d-none');
        $modificationSettings.classList.add('d-none');

        /* Делаем поле Name НЕ обязательным */
        Array.from($nameOfferSettings).forEach(e => e.removeAttribute('required'));
        Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));
        Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));


    } else {

        /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
        Array.from($nameOfferSettings).forEach(e => {
            if (e.name.match("postfix") === null) {
                e.setAttribute('required', true)
            }
        });

        $offerSettings.classList.remove('d-none');

        $checkboxVariationSettings.disabled = false;
        $checkboxModificationSettings.disabled = false;
    }
});


$checkboxVariationSettings.addEventListener('change', function () {


    if (this.checked == false) {
        $variationSettings.classList.add('d-none');

        /* Делаем поле Name НЕ обязательным */
        Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));

    } else {
        $variationSettings.classList.remove('d-none');

        /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
        Array.from($nameVariationSettings).forEach(e => {
            if (e.name.match("postfix") === null) {
                e.setAttribute('required', true)
            }
        });

    }
});


$checkboxModificationSettings.addEventListener('change', function () {

    if (this.checked == false) {
        $modificationSettings.classList.add('d-none');

        /* Делаем поле Name НЕ обязательным */
        Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));

    } else {
        $modificationSettings.classList.remove('d-none');

        /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
        Array.from($nameModificationSettings).forEach(e => {
            if (e.name.match("postfix") === null) {
                e.setAttribute('required', true)
            }
        });

    }
});


if ($checkboxOfferSettings.checked == false) {
    $offerSettings.classList.add('d-none');

    $checkboxVariationSettings.checked = false;
    $checkboxVariationSettings.disabled = true;

    $checkboxModificationSettings.checked = false;
    $checkboxModificationSettings.disabled = true;


    /* Делаем поле Name НЕ обязательным */

    Array.from($nameOfferSettings).forEach(e => e.removeAttribute('required'));

    Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));
    $variationSettings.classList.add('d-none');

    Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));
    $modificationSettings.classList.add('d-none');


} else {

    $offerSettings.classList.remove('d-none');

    $checkboxVariationSettings.disabled = false;
    $checkboxModificationSettings.disabled = false;

    /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
    Array.from($nameOfferSettings).forEach(e => {
        if (e.name.match("postfix") === null) {
            e.setAttribute('required', true)
        }
    });
}


if ($checkboxVariationSettings.checked == false) {
    /* Делаем поле Name НЕ обязательным */
    Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));

    $variationSettings.classList.add('d-none');

} else {
    /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
    Array.from($nameVariationSettings).forEach(e => {
        if (e.name.match("postfix") === null) {
            e.setAttribute('required', true)
        }
    });

    $variationSettings.classList.remove('d-none');
}


if ($checkboxModificationSettings.checked == false) {
    /* Делаем поле Name НЕ обязательным */
    Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));

    $modificationSettings.classList.add('d-none');

} else {
    /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
    Array.from($nameModificationSettings).forEach(e => {
        if (e.name.match("postfix") === null) {
            e.setAttribute('required', true)
        }
    });


    $modificationSettings.classList.remove('d-none');
}


function chanfeReferenc($this) {

    let ref = document.getElementById($this.dataset.reference);
    if ($this.checked === true) {
        ref.classList.remove('d-none');
    } else {
        ref.classList.add('d-none');
        ref.selectedIndex = 0; /* сбрасываем select */
    }
}


document.querySelectorAll('.change-postfix').forEach(function (postfix) {

    postfix.addEventListener('change', function ()
    {

        document.querySelectorAll('.' + this.id).forEach(function (element) {

            if (postfix.checked == true)
            {
                element.classList.remove('d-none');
                element.querySelector('input').setAttribute('required', true);
            }
            else
            {
                element.classList.add('d-none');
                let inpt = element.querySelector('input');
                inpt.removeAttribute('required');
                inpt.removeAttribute('value');
            }

        });
    });
});