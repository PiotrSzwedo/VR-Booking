
const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;


registerBlockType('ta-vr-all-reservation/all', {
    title: 'VR all Reservation',
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
                "VR ALL Reservation Block (Editor View)"
        );
    },

    save() {
        return null;
    },
});

registerBlockType('ta-vr-reservation/my', { 
    title: 'VR My Reservation',
    icon: 'admin-users',
    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
        },
    },

    edit({ attributes, setAttributes }) {
        return (
            "VR My Reservation Block (Editor View)"
        );
    },

    save() {
        return null; // Dynamic block; PHP will handle rendering
    },
});

registerBlockType('ta-vr-all-reservation/add', { 
    title: 'VR Add Vr',
    icon: 'admin-users',
    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
        },
    },

    edit({ attributes, setAttributes }) {
        return (
                "VR add new Vr (Editor View)"
        );
    },

    save() {
        return null; 
    },
});

registerBlockType('ta-vr-all-reservation/delete', { 
    title: 'VR Delete Vr',
    icon: 'admin-users',
    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
        },
    },

    edit({ attributes, setAttributes }) {
        return (
                "VR add new Vr (Editor View)"
        );
    },

    save() {
        return null; 
    },
});