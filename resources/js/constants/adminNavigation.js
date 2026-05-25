export const iconPaths = {
    dashboard: 'M3 3h7v7H3V3Zm11 0h7v7h-7V3ZM3 14h7v7H3v-7Zm11 0h7v7h-7v-7Z',
    package:
        'M12 2 3 6.5v11L12 22l9-4.5v-11L12 2Zm0 2.25 5.7 2.85L12 9.95 6.3 7.1 12 4.25ZM5 8.72l6 3v7.03l-6-3V8.72Zm14 7.03-6 3v-7.03l6-3v7.03Z',
    orders:
        'M7 3h10a2 2 0 0 1 2 2v16l-3-1.6-3 1.6-3-1.6L7 21V5a2 2 0 0 1 2-2Zm2 4v2h6V7H9Zm0 4v2h6v-2H9Zm0 4v2h4v-2H9Z',
    users:
        'M9 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0 2c-3.31 0-6 1.79-6 4v2h12v-2c0-2.21-2.69-4-6-4Zm8.5-2a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm0 2c-.9 0-1.75.17-2.5.47 1.22.9 2 2.12 2 3.53v2h5v-2c0-2.21-2.01-4-4.5-4Z',
    ruler:
        'M4 3h16a1 1 0 0 1 1 1v5.5a1 1 0 0 1-.29.71L10.21 20.71a1 1 0 0 1-1.42 0L3.29 15.21a1 1 0 0 1 0-1.42L13.79 3.29A1 1 0 0 1 14.5 3H4Zm10.91 2-9.5 9.5L9.5 18.59l9.5-9.5V5h-4.09ZM7.5 13.5l1 1L10 13l-1-1-1.5 1.5Zm3-3 1 1L13 10l-1-1-1.5 1.5Zm3-3 1 1L16 7l-1-1-1.5 1.5Z',
    login:
        'M10 4a1 1 0 0 1 1-1h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7a1 1 0 1 1 0-2h7V5h-7a1 1 0 0 1-1-1Zm1.29 4.29a1 1 0 0 1 1.42 0l3 3a1 1 0 0 1 0 1.42l-3 3a1 1 0 0 1-1.42-1.42L12.59 13H4a1 1 0 1 1 0-2h8.59l-1.3-1.29a1 1 0 0 1 0-1.42Z',
    settings:
        'M19.43 12.98c.04-.32.07-.65.07-.98s-.02-.66-.07-.98l2.11-1.65-2-3.46-2.49 1a7.03 7.03 0 0 0-1.69-.98L15 3.25h-4l-.36 2.68c-.61.24-1.18.56-1.69.98l-2.49-1-2 3.46 2.11 1.65c-.04.32-.07.65-.07.98s.02.66.07.98l-2.11 1.65 2 3.46 2.49-1c.51.4 1.08.74 1.69.98L11 20.75h4l.36-2.68c.61-.24 1.18-.58 1.69-.98l2.49 1 2-3.46-2.11-1.65ZM13 15.5A3.5 3.5 0 1 1 13 8a3.5 3.5 0 0 1 0 7.5Z',
};

export const menuGroups = [
    {
        title: 'Menu',
        items: [
            { label: 'Dashboard', icon: 'dashboard', hash: '#dashboard', children: ['Overview'] },
            { label: 'Products', icon: 'package', hash: '#products' },
            { label: 'Units', icon: 'ruler', hash: '#units' },
            { label: 'Orders', icon: 'orders', hash: '#orders' },
            { label: 'Customers', icon: 'users', hash: '#customers' },
        ],
    },
    {
        title: 'System',
        items: [
            { label: 'Login', icon: 'login', hash: '#login' },
            { label: 'Settings', icon: 'settings', hash: '#settings' },
        ],
    },
];
