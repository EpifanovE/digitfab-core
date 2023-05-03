import {useSelect} from '@wordpress/data';
import {store as coreStore} from '@wordpress/core-data';

export const useTaxonomies = (postType) => {
    return useSelect(
        (select) => {
            const {getTaxonomies} = select(coreStore);
            const filteredTaxonomies = getTaxonomies({
                type: postType,
                per_page: -1,
                context: 'view',
            });
            return filteredTaxonomies;
        },
        [postType]
    );
};