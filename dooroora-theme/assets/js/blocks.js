(function (wp) {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var Button = wp.components.Button;
    var __ = wp.i18n.__;

    /* ---- Entity List ---- */
    registerBlockType('dooroora/entity-list', {
        title: __('Related Components'),
        icon: 'list-view',
        category: 'widgets',
        attributes: {
            title: { type: 'string', default: __('Components Mentioned') },
            entities: { type: 'array', default: [] }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            function addEntity() {
                var entities = attributes.entities.concat([{ label: '', url: '' }]);
                setAttributes({ entities: entities });
            }
            function removeEntity(index) {
                setAttributes({ entities: attributes.entities.filter(function (_, i) { return i !== index; }) });
            }
            function updateEntity(index, key, value) {
                var entities = attributes.entities.map(function (entity, i) {
                    if (i === index) { var u = Object.assign({}, entity); u[key] = value; return u; }
                    return entity;
                });
                setAttributes({ entities: entities });
            }

            var items = attributes.entities.map(function (entity, index) {
                return el('div', { key: index, style: { marginBottom: '8px', padding: '8px', background: '#f0f0f0', borderRadius: '4px' } },
                    el(TextControl, { label: __('Entity Name'), value: entity.label, onChange: function (v) { updateEntity(index, 'label', v); } }),
                    el(TextControl, { label: __('URL (optional)'), value: entity.url, onChange: function (v) { updateEntity(index, 'url', v); } }),
                    el(Button, { isDestructive: true, isSmall: true, onClick: function () { removeEntity(index); } }, __('Remove'))
                );
            });

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Entity List Settings') },
                        el(TextControl, { label: __('Title'), value: attributes.title, onChange: function (v) { setAttributes({ title: v }); } }),
                        el(Button, { isPrimary: true, onClick: addEntity }, __('Add Entity'))
                    )
                ),
                el('div', { key: 'preview', className: 'wp-block-dooroora-entity-list', style: { padding: '16px', border: '1px solid #e2e8f0', borderRadius: '8px' } },
                    el('h3', { style: { fontFamily: 'Playfair Display, serif', fontSize: '1.25rem', marginBottom: '12px' } }, attributes.title),
                    attributes.entities.length === 0
                        ? el('p', { style: { color: '#64748b' } }, __('Add entities to create a component list.'))
                        : el('ul', { style: { listStyle: 'none', padding: '0' } },
                            attributes.entities.map(function (entity, i) {
                                return el('li', { key: i, style: { padding: '4px 0', display: 'flex', alignItems: 'center', gap: '8px' } },
                                    el('span', { style: { color: '#22c55e' } }, '\u2713 '),
                                    el('span', null, entity.label)
                                );
                            })
                        )
                )
            ];
        },
        save: function () { return null; }
    });

    /* ---- FAQ Schema ---- */
    registerBlockType('dooroora/faq', {
        title: __('FAQ Schema'),
        icon: 'editor-help',
        category: 'widgets',
        attributes: {
            title: { type: 'string', default: __('Frequently Asked Questions') },
            items: { type: 'array', default: [] }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            function addItem() {
                setAttributes({ items: attributes.items.concat([{ question: '', answer: '' }]) });
            }
            function removeItem(index) {
                setAttributes({ items: attributes.items.filter(function (_, i) { return i !== index; }) });
            }
            function updateItem(index, key, value) {
                var items = attributes.items.map(function (item, i) {
                    if (i === index) { var u = Object.assign({}, item); u[key] = value; return u; }
                    return item;
                });
                setAttributes({ items: items });
            }

            var list = attributes.items.map(function (item, index) {
                return el('div', { key: index, style: { marginBottom: '12px', padding: '12px', background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: '6px' } },
                    el(TextControl, { label: __('Question'), value: item.question, onChange: function (v) { updateItem(index, 'question', v); } }),
                    el(TextareaControl, { label: __('Answer'), value: item.answer, onChange: function (v) { updateItem(index, 'answer', v); } }),
                    el(Button, { isDestructive: true, isSmall: true, onClick: function () { removeItem(index); } }, __('Remove'))
                );
            });

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('FAQ Settings') },
                        el(TextControl, { label: __('Section Title'), value: attributes.title, onChange: function (v) { setAttributes({ title: v }); } }),
                        el(Button, { isPrimary: true, onClick: addItem }, __('Add Question'))
                    )
                ),
                el('div', { key: 'preview', className: 'wp-block-dooroora-faq', style: { padding: '16px', border: '1px solid #e2e8f0', borderRadius: '8px' } },
                    el('h2', { style: { fontFamily: 'Playfair Display, serif', fontSize: '1.5rem', marginBottom: '16px' } }, attributes.title),
                    attributes.items.length === 0
                        ? el('p', { style: { color: '#64748b' } }, __('Add FAQ items to create schema.') )
                        : attributes.items.map(function (item, i) {
                            return el('div', { key: i, style: { marginBottom: '8px', padding: '10px', background: '#f1f5f9', borderRadius: '4px' } },
                                el('strong', { style: { display: 'block', marginBottom: '4px' } }, item.question || __('(Question)')),
                                el('p', { style: { color: '#64748b', fontSize: '0.875rem', margin: '0' } }, item.answer || __('(Answer)'))
                            );
                        })
                )
            ];
        },
        save: function () { return null; }
    });

    /* ---- How-To Schema ---- */
    registerBlockType('dooroora/howto', {
        title: __('How-To Schema'),
        icon: 'editor-ol',
        category: 'widgets',
        attributes: {
            title: { type: 'string', default: __('How To') },
            description: { type: 'string', default: '' },
            steps: { type: 'array', default: [] }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            function addStep() {
                setAttributes({ steps: attributes.steps.concat([{ title: '', description: '' }]) });
            }
            function removeStep(index) {
                setAttributes({ steps: attributes.steps.filter(function (_, i) { return i !== index; }) });
            }
            function updateStep(index, key, value) {
                var steps = attributes.steps.map(function (s, i) {
                    if (i === index) { var u = Object.assign({}, s); u[key] = value; return u; }
                    return s;
                });
                setAttributes({ steps: steps });
            }

            var list = attributes.steps.map(function (step, index) {
                return el('div', { key: index, style: { marginBottom: '12px', padding: '12px', background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: '6px' } },
                    el(TextControl, { label: __('Step ' + (index + 1) + ' Title'), value: step.title, onChange: function (v) { updateStep(index, 'title', v); } }),
                    el(TextareaControl, { label: __('Step ' + (index + 1) + ' Description'), value: step.description, onChange: function (v) { updateStep(index, 'description', v); } }),
                    el(Button, { isDestructive: true, isSmall: true, onClick: function () { removeStep(index); } }, __('Remove'))
                );
            });

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('How-To Settings') },
                        el(TextControl, { label: __('Section Title'), value: attributes.title, onChange: function (v) { setAttributes({ title: v }); } }),
                        el(TextareaControl, { label: __('Description (optional)'), value: attributes.description, onChange: function (v) { setAttributes({ description: v }); } }),
                        el(Button, { isPrimary: true, onClick: addStep }, __('Add Step'))
                    )
                ),
                el('div', { key: 'preview', className: 'wp-block-dooroora-howto', style: { padding: '16px', border: '1px solid #e2e8f0', borderRadius: '8px' } },
                    el('h2', { style: { fontFamily: 'Playfair Display, serif', fontSize: '1.5rem', marginBottom: '8px' } }, attributes.title),
                    attributes.description
                        ? el('p', { style: { color: '#64748b', marginBottom: '16px' } }, attributes.description)
                        : null,
                    attributes.steps.length === 0
                        ? el('p', { style: { color: '#64748b' } }, __('Add steps to create How-To schema.'))
                        : el('ol', { style: { paddingLeft: '24px' } },
                            attributes.steps.map(function (step, i) {
                                return el('li', { key: i, style: { marginBottom: '12px' } },
                                    el('strong', null, step.title || __('(Step title)')),
                                    el('p', { style: { color: '#64748b', fontSize: '0.875rem', margin: '4px 0 0' } }, step.description || __('(Step description)'))
                                );
                            })
                        )
                )
            ];
        },
        save: function () { return null; }
    });
})(window.wp);
