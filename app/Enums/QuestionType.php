<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static TYPE_TEXT()
 * @method static static TYPE_TEXTAREA()
 * @method static static TYPE_SELECT()
 * @method static static TYPE_RADIO()
 * @method static static TYPE_CHECKBOX()
 */
final class QuestionType extends Enum
{
    const TYPE_TEXT =  'text';
    const TYPE_TEXTAREA =  'textarea';
    const TYPE_SELECT =  'select';
    const TYPE_RADIO =  'radio';
    const TYPE_CHECKBOX =  'checkbox';
}
