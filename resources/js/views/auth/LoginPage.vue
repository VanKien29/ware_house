<script setup>
import { reactive, ref } from 'vue';
import { apiRequest } from '../../services/apiClient';

const emit = defineEmits(['backToDashboard', 'loginSuccess']);

const form = reactive({
    email: 'manager@warehouse.test',
    password: '',
});
const isLoading = ref(false);
const errorMessage = ref('');

const login = async () => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const data = await apiRequest('/login', {
            method: 'POST',
            body: {
                email: form.email,
                password: form.password,
            },
        });

        emit('loginSuccess', {
            token: data.token,
            user: data.user,
        });
    } catch (error) {
        errorMessage.value = error.message || 'Không thể đăng nhập, vui lòng thử lại.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <main class="min-h-screen bg-gray-50 font-outfit text-gray-800 dark:bg-gray-950 dark:text-white/90">
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            <section class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-[420px]">
                    <button
                        class="mb-8 inline-flex items-center gap-2 text-theme-sm font-medium text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400"
                        type="button"
                        @click="$emit('backToDashboard')"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path
                                fill-rule="evenodd"
                                d="M11.78 4.22a.75.75 0 0 1 0 1.06L7.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Về dashboard
                    </button>

                    <div class="mb-8">
                        <span class="mb-6 grid size-12 place-items-center rounded-xl bg-brand-500 text-lg font-semibold text-white shadow-theme-sm">
                            W
                        </span>
                        <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Đăng nhập</h1>
                        <p class="mt-2 text-theme-sm text-gray-500 dark:text-gray-400">
                            Nhập email và mật khẩu để vào trang quản trị kho.
                        </p>
                    </div>

                    <form
                        class="space-y-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 sm:p-6"
                        @submit.prevent="login"
                    >
                        <div
                            v-if="errorMessage"
                            class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-theme-sm text-red-600 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400"
                        >
                            {{ errorMessage }}
                        </div>

                        <div>
                            <label class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-300" for="email">
                                Email
                            </label>
                            <input
                                id="email"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                placeholder="manager@warehouse.test"
                                type="email"
                                v-model="form.email"
                            />
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-3">
                                <label class="block text-theme-sm font-medium text-gray-700 dark:text-gray-300" for="password">
                                    Mật khẩu
                                </label>
                                <a class="text-theme-sm font-medium text-brand-500 hover:text-brand-600" href="#" @click.prevent>
                                    Quên mật khẩu?
                                </a>
                            </div>
                            <input
                                id="password"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                placeholder="••••••••"
                                type="password"
                                v-model="form.password"
                            />
                        </div>

                        <label class="flex items-center gap-3 text-theme-sm text-gray-700 dark:text-gray-300">
                            <input
                                class="size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700"
                                type="checkbox"
                                checked
                            />
                            Ghi nhớ đăng nhập
                        </label>

                        <button
                            class="flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-70"
                            type="submit"
                            :disabled="isLoading"
                        >
                            {{ isLoading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
                        </button>
                    </form>
                </div>
            </section>

            <aside class="hidden min-h-screen border-l border-gray-200 bg-white px-10 py-12 dark:border-gray-800 dark:bg-gray-900 lg:flex lg:flex-col lg:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-xl bg-brand-500 text-sm font-semibold text-white">
                            W
                        </span>
                        <span class="text-xl font-semibold text-gray-900 dark:text-white">Warehouse</span>
                    </div>

                    <div class="mt-20 max-w-[520px]">
                        <p class="text-theme-sm font-medium text-brand-500 dark:text-brand-400">Admin workspace</p>
                        <h2 class="mt-4 text-[40px] font-semibold leading-[48px] text-gray-900 dark:text-white">
                            Quản lý kho hàng gọn gàng từ một nơi.
                        </h2>
                        <p class="mt-5 text-theme-sm leading-6 text-gray-500 dark:text-gray-400">
                            Theo dõi sản phẩm, đơn vị tính, đơn hàng và khách hàng trong một giao diện thống nhất theo TailAdmin.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Modules</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">08</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Users</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">12</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Status</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">OK</p>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</template>
