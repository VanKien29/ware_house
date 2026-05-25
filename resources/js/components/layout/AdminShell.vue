<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import LoginPage from '../../views/auth/LoginPage.vue';
import BlankDashboard from '../../views/BlankDashboard.vue';
import UnitIndex from '../../views/units/UnitIndex.vue';
import { apiRequest } from '../../services/apiClient';
import { clearAuth, getStoredAuth, storeAuth } from '../../services/authStorage';
import TheHeader from './TheHeader.vue';
import TheSidebar from './TheSidebar.vue';

const activeMenu = ref('Dashboard');
const openMenu = ref('Dashboard');
const isCollapsed = ref(false);
const isMobileOpen = ref(false);
const isDarkMode = ref(false);
const authToken = ref('');
const currentUser = ref(null);
const postLoginMenu = ref('Dashboard');

const hashByMenu = {
    Dashboard: '#dashboard',
    Products: '#products',
    Units: '#units',
    Orders: '#orders',
    Customers: '#customers',
    Login: '#login',
    Settings: '#settings',
};

const menuByHash = Object.fromEntries(Object.entries(hashByMenu).map(([menu, hash]) => [hash, menu]));

const mainOffsetClass = computed(() => (isCollapsed.value ? 'lg:ml-[90px]' : 'lg:ml-[290px]'));
const isAuthScreen = computed(() => activeMenu.value === 'Login');
const isLoggedIn = computed(() => Boolean(authToken.value));

const protectedMenus = ['Dashboard', 'Products', 'Units', 'Orders', 'Customers', 'Settings'];

const requiresAuth = (menu) => protectedMenus.includes(menu);

const syncMenuFromHash = () => {
    const menu = menuByHash[window.location.hash] || 'Dashboard';

    if (requiresAuth(menu) && !isLoggedIn.value) {
        postLoginMenu.value = menu;
        activeMenu.value = 'Login';

        if (window.location.hash !== '#login') {
            window.location.hash = '#login';
        }

        return;
    }

    activeMenu.value = menu;

    if (menu === 'Dashboard') {
        openMenu.value = 'Dashboard';
    }
};

const setActiveMenu = (menu) => {
    if (requiresAuth(menu) && !isLoggedIn.value) {
        postLoginMenu.value = menu;
        activeMenu.value = 'Login';

        if (window.location.hash !== '#login') {
            window.location.hash = '#login';
        }

        return;
    }

    activeMenu.value = menu;

    if (hashByMenu[menu] && window.location.hash !== hashByMenu[menu]) {
        window.location.hash = hashByMenu[menu];
    }
};

const goToDashboard = () => {
    setActiveMenu('Dashboard');
    openMenu.value = 'Dashboard';
};

const handleLoginSuccess = (payload) => {
    authToken.value = payload.token;
    currentUser.value = payload.user;
    storeAuth(payload);
    setActiveMenu(postLoginMenu.value || 'Dashboard');
};

const handleLogout = async () => {
    const token = authToken.value;

    clearAuth();
    authToken.value = '';
    currentUser.value = null;
    postLoginMenu.value = 'Dashboard';

    if (token) {
        try {
            await apiRequest('/logout', { method: 'POST', token });
        } catch {
            // Local logout should still succeed if the token is already expired.
        }
    }

    setActiveMenu('Login');
};

const setOpenMenu = (menu) => {
    openMenu.value = menu;
};

const toggleSidebar = () => {
    if (window.innerWidth >= 1024) {
        isCollapsed.value = !isCollapsed.value;
        return;
    }

    isMobileOpen.value = !isMobileOpen.value;
};

const closeMobileSidebar = () => {
    isMobileOpen.value = false;
};

const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
};

onMounted(() => {
    const storedAuth = getStoredAuth();
    authToken.value = storedAuth.token || '';
    currentUser.value = storedAuth.user;
    isDarkMode.value = localStorage.getItem('theme') === 'dark';
    document.documentElement.classList.toggle('dark', isDarkMode.value);
    syncMenuFromHash();
    window.addEventListener('hashchange', syncMenuFromHash);
});

onUnmounted(() => {
    window.removeEventListener('hashchange', syncMenuFromHash);
});

watch(isDarkMode, (enabled) => {
    document.documentElement.classList.toggle('dark', enabled);
    localStorage.setItem('theme', enabled ? 'dark' : 'light');
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 font-outfit text-gray-800 dark:bg-gray-950 dark:text-white/90">
        <LoginPage v-if="isAuthScreen" @back-to-dashboard="goToDashboard" @login-success="handleLoginSuccess" />

        <template v-else>
        <button
            v-if="isMobileOpen"
            aria-label="Close sidebar"
            class="fixed inset-0 z-9999 cursor-default bg-gray-900/50 lg:hidden"
            type="button"
            @click="closeMobileSidebar"
        ></button>

        <TheSidebar
            :active-menu="activeMenu"
            :open-menu="openMenu"
            :is-collapsed="isCollapsed"
            :is-mobile-open="isMobileOpen"
            :select-menu-handler="setActiveMenu"
            :toggle-menu-handler="setOpenMenu"
            :close-mobile-sidebar-handler="closeMobileSidebar"
        />

        <div class="min-h-screen transition-all duration-300 ease-in-out" :class="mainOffsetClass">
            <TheHeader
                :is-dark-mode="isDarkMode"
                :is-mobile-open="isMobileOpen"
                :current-user="currentUser"
                @toggle-sidebar="toggleSidebar"
                @toggle-theme="toggleTheme"
                @logout="handleLogout"
            />

            <UnitIndex v-if="activeMenu === 'Units'" :token="authToken" @unauthorized="handleLogout" />
            <BlankDashboard v-else />
        </div>
        </template>
    </div>
</template>
