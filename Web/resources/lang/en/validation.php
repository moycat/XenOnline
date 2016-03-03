<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => '你必须接受:attribute。',
    'active_url'           => ':attribute不是一个有效的链接。',
    'after'                => ':attribute不能早于:date。',
    'alpha'                => ':attribute只能包含字母。',
    'alpha_dash'           => ':attribute只能包含字母、数字、短横线。',
    'alpha_num'            => ':attribute只能包含字母、数字。',
    'array'                => ':attribute必须是一个数组。',
    'before'               => ':attribute不能迟于:date。',
    'between'              => [
        'numeric' => ':attribute在:min至:max之间。',
        'file'    => ':attribute的大小必须在:min至:maxKiB之间。',
        'string'  => ':attribute的长度必须在:min至:max个字符之间。',
        'array'   => ':attribute的项目数必须在:min至:max之间。',
    ],
    'boolean'              => ':attribute只能是布尔值。',
    'confirmed'            => '两次输入的:attribute不匹配。',
    'date'                 => ':attribute不是一个有效的日期。',
    'date_format'          => ':attribute不符合格式：:format。',
    'different'            => ':attribute与:other必须不同。',
    'digits'               => ':attribute必须是:digits位数字。',
    'digits_between'       => ':attribute必须为长度在:min至:max之间的数字。',
    'email'                => ':attribute必须是有效的电子邮箱地址。',
    'exists'               => '选项:attribute无效。',
    'filled'               => ':attribute必填。',
    'image'                => ':attribute必须是图片。',
    'in'                   => ':attribute的值无效。',
    'integer'              => ':attribute必须是整数。',
    'ip'                   => ':attribute必须是有效的IP地址。',
    'json'                 => ':attribute必须是有效的JSON字符串。',
    'max'                  => [
        'numeric' => ':attribute不能大于:max.',
        'file'    => ':attribute不能大于:maxKiB。',
        'string'  => ':attribute的长度不能超过:max个字符。',
        'array'   => ':attribute的项目数不能大于:max。',
    ],
    'mimes'                => ':attribute必须是以下类型之一：:values。',
    'min'                  => [
        'numeric' => ':attribute不能小于:min。',
        'file'    => ':attribute不能小于:minKiB。',
        'string'  => ':attribute的长度不能少于:min个字符。',
        'array'   => ':attribute的项目数不能少于:min。',
    ],
    'not_in'               => ':attribute的值无效。',
    'numeric'              => ':attribute必须是数字。',
    'present'              => ':attribute字段必须出现。',
    'regex'                => ':attribute格式无效。',
    'required'             => ':attribute必填。',
    'required_if'          => '当:other为:value时，:attribute必填。',
    'required_unless'      => ':attribute必填，除非:other属于:values。',
    'required_with'        => '当:values时，:attribute必填。',
    'required_with_all'    => '当:values时，:attribute必填。',
    'required_without'     => '当不:values时，:attribute必填。',
    'required_without_all' => '当不:values时，:attribute必填。',
    'same'                 => ':attribute与:other必须相同。',
    'size'                 => [
        'numeric' => ':attribute位数必须为:size。',
        'file'    => ':attribute的大小必须为:sizeKiB。',
        'string'  => ':attribute的长度必须为:size个字符。',
        'array'   => ':attribute必须包含:size个项目。',
    ],
    'string'               => ':attribute必须是字符串。',
    'timezone'             => ':attribute必须为有效的时区。',
    'unique'               => '相同的:attribute已存在。',
    'url'                  => ':attribute的格式无效。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
