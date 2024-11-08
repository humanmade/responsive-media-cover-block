import { PanelBody, PanelRow, Button, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { createHigherOrderComponent } from '@wordpress/compose';
import { registerPlugin } from '@wordpress/plugins';
import { Fragment } from '@wordpress/element';
import { InspectorControls, MediaUploadCheck } from '@wordpress/block-editor';
import { MediaUpload } from '@wordpress/media-utils';

// Create a Higher Order Component (HOC) to add attributes to the Cover block
const withMobileAttributes = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { attributes, setAttributes } = props;
		const mobileMediaId = attributes?.mobileMediaId;
		const mobileMediaDetails = useSelect( select => {
			const media = select('core').getMedia( mobileMediaId );
			const size = media?.media_details.sizes?.thumbnail || {};

			return media ? {
				id: media.id,
				alt: media.alt_text || '',
				src: size.source_url || media.source_url,
				width: size.width || media.width,
				height: size.height || media.height,
				type: media.mime_type.replace( /\/.+$/, '' ),
				file: media.media_details.file || '',
			} : null;
		}, [ mobileMediaId ] );

		if ( props.name !== 'core/cover' ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody title="Mobile Media" initialOpen={ true }>
						<PanelRow>
							<p>Select a media file to be shown on mobile.</p>
						</PanelRow>
						<PanelRow>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) => setAttributes( { mobileMediaId: media.id } ) }
									allowedTypes={ [ 'image', 'video' ] }
									render={ ( { open } ) => (
										<Button onClick={ open } isDefault isLarge>
											{ attributes.mobileMediaId ? 'Change Mobile Media' : 'Select Mobile Media' }
										</Button>
									) }
								/>
							</MediaUploadCheck>
						</PanelRow>
						{ attributes.mobileMediaId && (
							<PanelRow>
								{ ( mobileMediaDetails?.type === 'image' ) && (
									<img src={ mobileMediaDetails.src } alt={ mobileMediaDetails.alt } />
								) }

								{ ( mobileMediaDetails?.type === 'video' ) && (
									<Fragment>{ `Video selected ${ mobileMediaDetails.file }` }</Fragment>
								) }

								{ ( ! mobileMediaDetails && mobileMediaId ) && (
									<Spinner />
								) }
							</PanelRow>
						) }
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'withMobileAttributes' );

// Register the new attributes
const addMobileAttributes = ( settings, name ) => {
	if ( name !== 'core/cover' ) {
		return settings;
	}

	return {
		...settings,
		attributes: {
			...settings.attributes,
			mobileMediaId: {
				type: 'number',
			},
		},
	};
};

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'hm-responsive-media-cover-block/attributes',
	addMobileAttributes
);

wp.hooks.addFilter(
	'editor.BlockEdit',
	'hm-responsive-media-cover-block/with-mobile-attributes',
	withMobileAttributes
);

// Register the plugin
registerPlugin( 'hm-responsive-media-cover-block', {
	render: () => null,
} );
