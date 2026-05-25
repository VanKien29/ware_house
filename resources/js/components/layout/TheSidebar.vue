<script setup>
import BaseSvgIcon from '../base/BaseSvgIcon.vue';
import { iconPaths, menuGroups } from '../../constants/adminNavigation';

const props = defineProps({
    activeMenu: {
        type: String,
        required: true,
    },
    openMenu: {
        type: String,
        required: true,
    },
    isCollapsed: {
        type: Boolean,
        required: true,
    },
    isMobileOpen: {
        type: Boolean,
        required: true,
    },
    selectMenuHandler: {
        type: Function,
        required: true,
    },
    toggleMenuHandler: {
        type: Function,
        required: true,
    },
    closeMobileSidebarHandler: {
        type: Function,
        required: true,
    },
});

const selectMenu = (item) => {
    props.selectMenuHandler(item.label);

    if (item.children?.length) {
        props.toggleMenuHandler(props.openMenu === item.label ? '' : item.label);
    }

    props.closeMobileSidebarHandler();
};

const selectDashboard = () => {
    props.selectMenuHandler('Dashboard');
};
</script>

<template>
    <aside
        class="fixed left-0 top-0 z-99999 flex h-screen flex-col border-r border-gray-200 bg-white px-5 text-gray-900 transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900 dark:text-white/90"
        :class="[
            isCollapsed ? 'lg:w-[90px]' : 'lg:w-[290px]',
            isMobileOpen ? 'w-[290px] translate-x-0' : 'w-[290px] -translate-x-full lg:translate-x-0',
        ]"
    >
        <div class="flex h-[88px] items-center" :class="isCollapsed ? 'lg:justify-center' : 'justify-start'">
            <a class="flex items-center gap-3" href="#dashboard" @click="selectDashboard">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-500 text-sm font-semibold text-white shadow-theme-sm">
                    W
                </span>
                <span class="text-xl font-semibold tracking-normal text-gray-900 dark:text-white" :class="isCollapsed ? 'lg:hidden' : ''">
                    Warehouse
                </span>
            </a>
        </div>

        <nav class="custom-scrollbar flex-1 overflow-y-auto pb-6">
            <div v-for="group in menuGroups" :key="group.title" class="mb-6">
                <h2
                    class="mb-4 flex text-xs font-medium uppercase leading-5 text-gray-400"
                    :class="isCollapsed ? 'lg:justify-center' : 'justify-start'"
                >
                    <span :class="isCollapsed ? 'lg:hidden' : ''">{{ group.title }}</span>
                    <svg
                        :class="isCollapsed ? 'hidden lg:block' : 'hidden'"
                        aria-hidden="true"
                        class="size-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path d="M4 10a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm4 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm4 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Z" />
                    </svg>
                </h2>

                <ul class="flex flex-col gap-4">
                    <li v-for="item in group.items" :key="item.label">
                        <a
                            :href="item.hash || '#'"
                            class="menu-item group"
                            :class="[
                                activeMenu === item.label || openMenu === item.label ? 'menu-item-active' : 'menu-item-inactive',
                                isCollapsed ? 'lg:justify-center' : 'justify-start',
                            ]"
                            @click="selectMenu(item)"
                        >
                            <BaseSvgIcon
                                :path="iconPaths[item.icon]"
                                size-class="size-5 shrink-0"
                                :class="activeMenu === item.label || openMenu === item.label ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                            />
                            <span class="menu-item-text" :class="isCollapsed ? 'lg:hidden' : ''">{{ item.label }}</span>
                            <span
                                v-if="item.badge"
                                class="ml-auto rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium uppercase text-brand-500 dark:bg-brand-500/15 dark:text-brand-400"
                                :class="isCollapsed ? 'lg:hidden' : ''"
                            >
                                {{ item.badge }}
                            </span>
                            <svg
                                v-if="item.children?.length"
                                class="ml-auto size-5 transition-transform duration-200"
                                :class="[openMenu === item.label ? 'rotate-180 text-brand-500' : 'text-gray-500', isCollapsed ? 'lg:hidden' : '']"
                                aria-hidden="true"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </a>

                        <div v-if="item.children?.length && openMenu === item.label" :class="isCollapsed ? 'lg:hidden' : ''">
                            <ul class="ml-9 mt-2 space-y-1">
                                <li v-for="child in item.children" :key="child">
                                    <a class="menu-dropdown-item menu-dropdown-item-active" href="#" @click.prevent>
                                        {{ child }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>
</template>
