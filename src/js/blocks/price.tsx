import * as React from 'react';
import {registerBlockType,} from '@wordpress/blocks';
import {currencyDollar} from "@wordpress/icons";
import {
    useBlockProps,
} from '@wordpress/block-editor';
import { useSelect} from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

const BlockEdit = (props) => {

    const {context} = props;
    const {postType, postId} = context;

    const postData = useSelect(
        ( select ) => {
            const { getEditedEntityRecord } = select( coreStore );
            return  getEditedEntityRecord(
                'postType',
                postType,
                postId
            );
        },
        [ postType, postId ]
    );

    const {_price_prefix, _price_suffix, _price} = postData.meta;
    const {df_currency} = postData;

    const parts = [
        _price_prefix,
        _price,
        df_currency,
        _price_suffix,
    ];

    return (
        <div {...useBlockProps()}>
            {(!!_price_prefix || !!_price || !!_price_suffix) && parts.filter(item => !!item).join(' ')}
        </div>
    );
};

registerBlockType('digitfab/price', {
    icon: currencyDollar,
    edit: BlockEdit
});

