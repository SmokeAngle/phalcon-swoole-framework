<?php
/**
 * @author 陈俊杰 chenjunjie@rainbowcn.com
 * 验证会员信息 公用类
 */
namespace App\Services\Thrift\Member\Validation;

use App\Services\Thrift\BaseValidation;

class MemberValidation extends BaseValidation
{
    public $validateRule = [
        'member_id'  => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The memberid is required'] ], ],
        'name'      => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The name is required'] ], ],
        'key'       => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The key is required'] ], ],
        'value'     => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The value is required'] ], ],
        'source'    => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The source is required'] ], ],
        'unionid'   => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The unionid is required'] ], ],
        'phone'     => [
                            [ 'Phalcon\Validation\Validator\PresenceOf',   ['message' => 'The phone is required'] ],
                            [ 'Phalcon\Validation\Validator\StringLength', ['max' => 11, 'min' => 11, 'messageMaximum' => 'The phone so long not valid', 'messageMinimum' => 'The phone so short not valid']  ]
                       ],
        'openid'    => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The openid is required'] ], ],
        'platform'  => [
                            [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The platform is required'] ],
                            [ 'Phalcon\Validation\Validator\Between',     ['maximum' => 10, 'minimum' => 1, 'message' => 'The platform not valid'] ]
                        ],
        'username'  => [
                            [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The username is required'] ],
                            [ 'Phalcon\Validation\Validator\StringLength', ['max' => 30, 'min' => 6, 'messageMaximum' => 'The username not valid', 'messageMinimum' => 'The username not valid'] ]
                       ],
        'password'  => [
                            [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The password is required'] ],
                            [ 'Phalcon\Validation\Validator\StringLength',['max' => 30, 'min' => 6, 'messageMaximum' => 'The password not valid', 'messageMinimum' => 'The password not valid']]
                       ],
        'store_code'=> [
                            [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The store_code is required'] ],
                            [ 'Phalcon\Validation\Validator\StringLength',['max' => 5, 'min' => 5, 'messageMaximum' => 'The store_code not valid', 'messageMinimum' => 'The store_code not valid'] ]
                       ],
        'member_thirdparty_id'  => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The member_thirdparty_id is required'] ], ],
        'member_address_id'       => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The member_address_id is required'] ], ],
        'card_no'               => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The card_no is required'] ], ],
        'realname'              => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The realname is required'] ], ],
        'id_photo_face'         => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The id_photo_face is required'] ], ],
        'id_photo_back'         => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The id_photo_back is required'] ], ],
        'idno'                  => [ [ 'Phalcon\Validation\Validator\PresenceOf',  ['message' => 'The idno is required'] ],
        ],
    ];
}
