/**
 * Styles.
 */
import './styles/editor.scss';
import './styles/style.scss';

/**
 * Internal dependencies
 */
import edit from './edit';
import deprecated from './deprecated';
import DimensionsAttributes from '../../components/dimensions-control/attributes';
import getEditWrapperProps from './getEditWrapperProps';
import icon from './icon';
import metadata from './block.json';
import save from './save';
import transforms from './transforms';
import { BackgroundAttributes } from '../../components/background';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

/**
 * Block constants
 */
const { name, category } = metadata;

const attributes = {
	...DimensionsAttributes,
	...BackgroundAttributes,
	...metadata.attributes,
};

const settings = {
	title: __( 'Row' ),
	description: __( 'Add a structured wrapper for column blocks, then add content blocks you’d like to the columns.' ),
	icon,
	keywords: [	__( 'rows' ), __( 'columns' ), __( 'layouts' )	],
	supports: {
		align: [ 'wide', 'full' ],
		anchor: true,
		stackedOnMobile: true,
		coBlocksSpacing: true,
	},
	attributes,
	transforms,
	edit,
	getEditWrapperProps,
	save,
	deprecated,
};

export { name, category, metadata, settings };
