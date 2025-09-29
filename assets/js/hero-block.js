( function( blocks, element, components, editor, i18n ) {
    const { __ } = i18n;
    const { registerBlockType } = blocks;
    const { Fragment } = element;
    const { PanelBody, RangeControl, ToggleControl, SelectControl, TextControl } = components;
    const { InspectorControls, MediaUpload, MediaUploadCheck, URLInputButton } = editor;

    registerBlockType( 'pde/hero', {
        title: __( 'Hero Pool de Elias', 'pool-de-elias' ),
        icon: 'slides',
        category: 'layout',
        attributes: {
            title: { type: 'string', default: __( 'Pool de Elias', 'pool-de-elias' ) },
            subtitle: { type: 'string', default: __( 'Competiciones de pool profesionales', 'pool-de-elias' ) },
            ctaText: { type: 'string', default: __( 'Explorar competiciones', 'pool-de-elias' ) },
            ctaUrl: { type: 'string', default: '' },
            mediaId: { type: 'number', default: 0 },
            overlay: { type: 'number', default: 0.4 },
            layout: { type: 'string', default: 'center' },
            height: { type: 'string', default: 'md' },
            showCompetitionCTA: { type: 'boolean', default: true },
        },
        edit: ( props ) => {
            const { attributes, setAttributes } = props;
            const { title, subtitle, ctaText, ctaUrl, mediaId, overlay, layout, height, showCompetitionCTA } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Diseño', 'pool-de-elias' ) }>
                            <SelectControl
                                label={ __( 'Alineación del contenido', 'pool-de-elias' ) }
                                value={ layout }
                                options={ [
                                    { label: __( 'Centrado', 'pool-de-elias' ), value: 'center' },
                                    { label: __( 'Izquierda', 'pool-de-elias' ), value: 'left' },
                                    { label: __( 'Derecha', 'pool-de-elias' ), value: 'right' },
                                ] }
                                onChange={ ( value ) => setAttributes( { layout: value } ) }
                            />
                            <SelectControl
                                label={ __( 'Altura', 'pool-de-elias' ) }
                                value={ height }
                                options={ [
                                    { label: __( 'Pequeña', 'pool-de-elias' ), value: 'sm' },
                                    { label: __( 'Media', 'pool-de-elias' ), value: 'md' },
                                    { label: __( 'Grande', 'pool-de-elias' ), value: 'lg' },
                                ] }
                                onChange={ ( value ) => setAttributes( { height: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Opacidad del overlay', 'pool-de-elias' ) }
                                value={ overlay }
                                min={ 0 }
                                max={ 0.8 }
                                step={ 0.05 }
                                onChange={ ( value ) => setAttributes( { overlay: value } ) }
                            />
                            <ToggleControl
                                label={ __( 'Mostrar CTA a competiciones', 'pool-de-elias' ) }
                                checked={ showCompetitionCTA }
                                onChange={ ( value ) => setAttributes( { showCompetitionCTA: value } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Imagen de fondo', 'pool-de-elias' ) }>
                            <MediaUploadCheck>
                                <MediaUpload
                                    onSelect={ ( media ) => setAttributes( { mediaId: media.id } ) }
                                    allowedTypes={ [ 'image' ] }
                                    value={ mediaId }
                                    render={ ( { open } ) => (
                                        <button type="button" className="components-button is-secondary" onClick={ open }>
                                            { mediaId ? __( 'Cambiar imagen', 'pool-de-elias' ) : __( 'Seleccionar imagen', 'pool-de-elias' ) }
                                        </button>
                                    ) }
                                />
                            </MediaUploadCheck>
                        </PanelBody>
                    </InspectorControls>
                    <div className={ `pde-hero-editor pde-hero-editor--${ layout } pde-hero-editor--${ height }` }>
                        <TextControl
                            label={ __( 'Título', 'pool-de-elias' ) }
                            value={ title }
                            onChange={ ( value ) => setAttributes( { title: value } ) }
                        />
                        <TextControl
                            label={ __( 'Subtítulo', 'pool-de-elias' ) }
                            value={ subtitle }
                            onChange={ ( value ) => setAttributes( { subtitle: value } ) }
                        />
                        <TextControl
                            label={ __( 'Texto del botón principal', 'pool-de-elias' ) }
                            value={ ctaText }
                            onChange={ ( value ) => setAttributes( { ctaText: value } ) }
                        />
                        <URLInputButton
                            label={ __( 'Enlace principal', 'pool-de-elias' ) }
                            url={ ctaUrl }
                            onChange={ ( value ) => setAttributes( { ctaUrl: value } ) }
                        />
                    </div>
                </Fragment>
            );
        },
        save: () => null,
    } );
} )( window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor || window.wp.editor, window.wp.i18n );
