/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
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


    // /* кнопки Добавить поле */
    // let $addButtonField = document.querySelectorAll('[id^="field_addCollection_"]');
    // $addButtonField.forEach(function (item) {
    //     //item.addEventListener('click', function (event) {
    //     //console.log(this.dataset.index);
    //     fieldCollection(item.dataset.index);
    //     //});
    // });


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
        newForm = newForm.replace(/__name__/g, index);
        //newForm = newForm.replace(/__FIELDS__/g, 0);
        newForm = newForm.replace(/__FIELDS_INDEX__/g, 1);


        /* Вставляем новую коллекцию */
        let div = document.createElement('div');
        div.id = 'item-collection-section-' + index;

        div.classList.add('card');
        div.classList.add('card-flush');
        div.classList.add('p-4');
        div.classList.add('mb-3');
        div.classList.add('item-collection-section');


        div.innerHTML = newForm;
        $blockCollection.append(div);


        let field = document.getElementById('field-collection-' + index);
        field.innerHTML = field.innerHTML.replace(/__FIELDS__/g, '0')
            .replace(/__FIELD_SORT__/g, '100');

        //createSectionField($btnAddFields)

        /* Удаляем при клике СЕКЦИЮ */
        deleteSection(div);
        /* Удаляем при клике СВОЙСТВО */
        deleteField(div);


        /* Удаляем при клике СЕКЦИЮ */
        // div.querySelector('.del-item-section').addEventListener('click', function () {
        //     let $counter = $blockCollection.getElementsByClassName('item-collection-section').length;
        //     if ($counter > 1) {
        //         this.closest('.item-collection-section').remove();
        //     }
        //     else
        //     {
        //         alert('Минимально должна быть добавлена одна секция');
        //     }
        // });

        /* Увеличиваем data-index на 1 после вставки новой коллекции */
        this.dataset.index = (index + 1).toString();


        /* получаем кнопку Добавить поле в секцию */

        // div.querySelector('.section-field_add-collection').addEventListener('click', function () {
        //     createSectionField(this);
        // });


        /* получаем количество коллекций и присваеваем data-index прототипу */
        //let $index = $blockCollection.getElementsByClassName('item-collection').length;


        /* Добавляем новую коллекцию FIELD */
        //fieldCollection(index);

        /* Получаем блок колекций FIELD в новой коллекции SECTION */
        //let $blockCollectionFields = document.getElementById('field_collection_'+index);

        /* Добавляем в форму FIELD */
        //createField($blockCollectionFields, index);

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

    let $delItem = $block.querySelectorAll('.del-item-field');

    /* Удаляем при клике свойство из секции */
    $delItem.forEach(function (item) {
        item.addEventListener('click', function () {

            console.log($block);

            console.log($block.querySelectorAll('.item-collection-field'));

            let $counter = $block.querySelectorAll('.item-collection-field').length;

            if ($counter > 1) {
                item.closest('.item-collection-field').remove();
            } else {
                alert('Минимально должна быть добавлена одна секция');
            }
        });
    });
}


/** Добавить свойство в секцию  */
function createSectionField($btnAddFields) {

    //console.log($btnAddFields);

    /* получаем прототип коллекции  */
    let newForm = $btnAddFields.dataset.prototype;
    let section_id = $btnAddFields.dataset.section;
    let index = $btnAddFields.dataset.index;

    /* Замена '__name__' в HTML-коде прототипа на
                вместо этого будет число, основанное на том, сколько коллекций */
    newForm = newForm.replace(/__name__/g, section_id);
    newForm = newForm.replace(/__FIELDS__/g, index);
    newForm = newForm.replace(/__FIELD_SORT__/g, index * 100 + 100);


    /* Вставляем новую коллекцию */
    let div = document.createElement('div');
    div.id = 'item-collection-field-' + index;
    div.classList.add('item-collection-field');
    div.classList.add('pb-3');

    div.innerHTML = newForm;

    document.getElementById('field-collection-' + section_id).append(div);
    /* блок коллекции 'field-collection-'+section_id */

    /* Удаляем при клике СВОЙСТВО */
    deleteField(div);

    $btnAddFields.dataset.index = (index * 1 + 1).toString();

    //div.innerHTML = newForm;
    //$blockCollection.append(div);

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


/**
 *
 * Добавить коллекцию ТОРГОВОГО ПРЕДЛОЖЕНИЯ
 *
 *
 */


/* кнопка Добавить коллекцию ТОРГОВОГО ПРЕДЛОЖЕНИЯ */
 let $addButtonOffers = document.getElementById('offers_addCollection');

/* Блок для новой коллекции Торговых предложений */
let $blockCollectionOffers = document.getElementById('offers_collection');

if($blockCollectionOffers)
{

    /* добавить событие на удаление ко всем существующим элементам формы в блок с классом .del-item */
    let $delItemOffers = $blockCollectionOffers.querySelectorAll('.del-item-offers');


    /* Удаляем при клике колекцию СЕКЦИЙ */
    $delItemOffers.forEach(function (item) {
        item.addEventListener('click', function () {

            item.closest('.item-collection-offers').remove();

        });
    });


    /* получаем количество коллекций и присваиваем data-index прототипу */
    //$blockCollectionOffers.dataset.index = $blockCollectionOffers.getElementsByClassName('item-collection-offers').length.toString();


    /* Добавляем новую коллекцию */
    $addButtonOffers.addEventListener('click', function ()
    {
        //console.log(this.dataset.prototype);
        //console.log(this.dataset.index);


        /* получаем прототип коллекции  */
        let newForm = this.dataset.prototype;
        let index = this.dataset.index;


        /* Замена '__name__' в HTML-коде прототипа на
            вместо этого будет число, основанное на том, сколько коллекций */
         newForm = newForm.replace(/__name__/g, index);
         newForm = newForm.replace(/__OFFER_SORT__/g, index * 100 + 100);
        //
        //
        // /* Вставляем новую коллекцию ТОРГОВЫХ ПРЕДЛОЖЕНИЙ */
         let div = document.createElement('div');
         div.innerHTML = newForm;
         $blockCollectionOffers.append(div);


        /* Удаляем при клике колекцию ТОРГОВЫХ ПРЕДЛОЖЕНИЙ */
        div.querySelector('.del-item-offers').addEventListener('click', function () {
            this.closest('.item-collection-offers').remove();
        });

        /* Увеличиваем data-index на 1 после вставки новой коллекции */
        this.dataset.index = (index * 1 + 1).toString();
        //
        $blockCollectionOffers.querySelectorAll('.is-reference').forEach(function (isReference) {

            isReference.addEventListener('change', function () {
                chanfeReferenc(this);
            });
        });
    });


    $blockCollectionOffers.querySelectorAll('.is-reference').forEach(function (isReference) {

        /* Обрабатываем уже существующие и сохраненные ТП */
        chanfeReferenc(isReference);

        isReference.addEventListener('change', function () {
            chanfeReferenc(this);
        });

    });
}


/** Получаем поле 'Название раздела' по локали для 'Символьный код категории' */
let $name = document.querySelector("input[data-lang='category_form_trans_0_"+$lang+"']");

if ($name) {
    $name.addEventListener('input', catUrl.debounce(500));

    function catUrl() {
        /* Заполняем транслитом URL */
        document.getElementById('category_form_info_url').value = translitRuEn(this.value).toLowerCase();
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


function chanfeReferenc($this) {


    let ref = document.getElementById($this.dataset.reference);

    let multipleDiv = document.getElementById('multiple_' + $this.dataset.multiple);
    let multiple = document.getElementById($this.dataset.multiple);

    if ($this.checked === true) {
        ref.classList.remove('d-none');
        multipleDiv.classList.remove('d-none');
    } else {
        multipleDiv.classList.add('d-none');
        multiple.checked = false;

        ref.classList.add('d-none');
        ref.selectedIndex = 0; /* сбрасываем select */

    }
}


//function fieldCollection(index) {

    // /** Коллекция FIELDS */
    //
    // /* кнопка Добавить коллекцию */
    // let $addButtonFields = document.getElementById('field_addCollection_'+index);
    //
    // /* Блок для новой коллекции */
    // let $blockCollectionFields = document.getElementById('field_collection_'+index);
    //
    // /* получаем количество коллекций и присваиваем data-index прототипу */
    // $blockCollectionFields.dataset.index = $blockCollectionFields.getElementsByClassName('item-collection-field').length.toString();
    //
    // $addButtonFields.addEventListener('click', function () {
    //     /* Добавляем в форму FIELD */
    //     createField($blockCollectionFields, index);
    // });
//}


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


