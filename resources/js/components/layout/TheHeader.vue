<script setup>
defineProps({
    isDarkMode: {
        type: Boolean,
        required: true,
    },
    isMobileOpen: {
        type: Boolean,
        required: true,
    },
    currentUser: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['toggleSidebar', 'toggleTheme', 'logout']);
</script>

<template>
    <header class="sticky top-0 z-9999 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
            <div class="flex w-full items-center justify-between gap-3 border-b border-gray-200 px-3 py-3 dark:border-gray-800 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4">
                <button
                    aria-label="Toggle sidebar"
                    class="z-99999 flex size-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 lg:size-11"
                    type="button"
                    @click="emit('toggleSidebar')"
                >
                    <svg v-if="isMobileOpen" class="size-6 fill-current lg:hidden" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6.22 7.28a.75.75 0 0 1 1.06-1.06L12 10.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L13.06 12l4.72 4.72a.75.75 0 1 1-1.06 1.06L12 13.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L10.94 12 6.22 7.28Z" />
                    </svg>
                    <svg v-else class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 10.5a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75ZM2.75 9.25a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5Z" />
                    </svg>
                </button>

                <a class="flex items-center gap-2 lg:hidden" href="#" @click.prevent>
                    <span class="grid size-8 place-items-center rounded-lg bg-brand-500 text-xs font-semibold text-white">W</span>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Warehouse</span>
                </a>

                <label class="hidden min-w-[280px] max-w-[430px] flex-1 items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-gray-500 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 lg:flex">
                    <svg class="size-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path
                            fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 1 0 3.47 9.77l2.63 2.63a.75.75 0 0 0 1.06-1.06l-2.63-2.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <input
                        class="h-6 w-full bg-transparent text-sm text-gray-800 outline-none placeholder:text-gray-400 dark:text-white/90"
                        placeholder="Search"
                        type="search"
                    />
                </label>
            </div>

            <div class="flex w-full items-center justify-end gap-3 px-5 py-4 shadow-theme-md lg:w-auto lg:px-0 lg:shadow-none">
                <button
                    aria-label="Toggle theme"
                    class="flex size-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs hover:bg-gray-50 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                    type="button"
                    @click="emit('toggleTheme')"
                >
                    <svg v-if="isDarkMode" class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 2a.75.75 0 0 1 .75.75V4a.75.75 0 0 1-1.5 0V2.75A.75.75 0 0 1 10 2Zm0 13.25a.75.75 0 0 1 .75.75v1.25a.75.75 0 0 1-1.5 0V16a.75.75 0 0 1 .75-.75ZM18 10a.75.75 0 0 1-.75.75H16a.75.75 0 0 1 0-1.5h1.25A.75.75 0 0 1 18 10ZM4.75 10a.75.75 0 0 1-.75.75H2.75a.75.75 0 0 1 0-1.5H4a.75.75 0 0 1 .75.75ZM14.95 5.05a.75.75 0 0 1 1.06 1.06l-.88.88a.75.75 0 1 1-1.06-1.06l.88-.88ZM5.93 14.07a.75.75 0 0 1 0 1.06l-.88.88a.75.75 0 0 1-1.06-1.06l.88-.88a.75.75 0 0 1 1.06 0ZM16.01 14.95a.75.75 0 0 1-1.06 1.06l-.88-.88a.75.75 0 0 1 1.06-1.06l.88.88ZM5.93 5.93a.75.75 0 0 1-1.06 1.06l-.88-.88A.75.75 0 0 1 5.05 5.05l.88.88ZM10 6.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Z" />
                    </svg>
                    <svg v-else class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M17.29 13.02A7.5 7.5 0 0 1 6.98 2.71a.75.75 0 0 0-.88-1.03 9 9 0 1 0 12.22 12.22.75.75 0 0 0-1.03-.88Z" />
                    </svg>
                </button>

                <button
                    aria-label="Notifications"
                    class="relative flex size-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs hover:bg-gray-50 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                    type="button"
                >
                    <span class="absolute right-2.5 top-2.5 size-2 rounded-full bg-orange-500"></span>
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 2a5 5 0 0 0-5 5v2.5c0 .68-.24 1.34-.68 1.86l-.9 1.08A1.5 1.5 0 0 0 4.57 15h10.86a1.5 1.5 0 0 0 1.15-2.56l-.9-1.08A2.9 2.9 0 0 1 15 9.5V7a5 5 0 0 0-5-5Zm0 16a2.5 2.5 0 0 0 2.29-1.5H7.71A2.5 2.5 0 0 0 10 18Z" />
                    </svg>
                </button>

                <button class="flex items-center gap-3 rounded-full text-left" type="button" @click="emit('logout')">
                    <span class="grid size-11 place-items-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        {{ currentUser?.name?.slice(0, 2).toUpperCase() || 'AD' }}
                    </span>
                    <span class="hidden lg:block">
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ currentUser?.name || 'Admin' }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Đăng xuất</span>
                    </span>
                </button>
            </div>
        </div>
    </header>
</template>
