const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;

registerBlockType('ta-pm/login', {
    title: 'TA Login panel',
    icon: 'admin-users',
    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html'
        },
    },

    edit({ attributes, setAttributes }) {
        return (
            "Login panel"
        );
    },

    save() {
        return null;
    },
});

registerBlockType('ta-pm/register', {
    title: 'TA Create user panel',
    icon: 'admin-users',
    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html'
        },
    },

    edit({ attributes, setAttributes }) {
        return (
            "Create user panel"
        );
    },

    save() {
        return null;
    },
});