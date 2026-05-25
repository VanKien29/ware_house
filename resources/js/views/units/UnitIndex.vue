<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { apiRequest } from '../../services/apiClient';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['unauthorized']);

const units = ref([]);
const search = ref('');
const isLoading = ref(false);
const isSaving = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const form = reactive({
    id: null,
    code: '',
    name: '',
    precision: 0,
});

const isEditing = computed(() => Boolean(form.id));

const filteredUnits = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return units.value;
    }

    return units.value.filter((unit) => {
        return [unit.code, unit.name, String(unit.precision)].some((value) => value?.toLowerCase().includes(keyword));
    });
});

const summaryCards = computed(() => [
    { label: 'Tổng đơn vị', value: String(units.value.length).padStart(2, '0'), note: 'Từ API' },
    { label: 'Đang hiển thị', value: String(filteredUnits.value.length).padStart(2, '0'), note: search.value ? 'Đã lọc' : 'Tất cả' },
    {
        label: 'Độ chính xác cao nhất',
        value: String(Math.max(0, ...units.value.map((unit) => Number(unit.precision || 0)))),
        note: 'Chữ số',
    },
]);

const getErrorMessage = (error) => {
    const firstValidationError = Object.values(error.errors || {})[0]?.[0];

    return firstValidationError || error.message || 'Không thể xử lý yêu cầu.';
};

const resetForm = () => {
    form.id = null;
    form.code = '';
    form.name = '';
    form.precision = 0;
};

const loadUnits = async () => {
    if (!props.token) {
        return;
    }

    isLoading.value = true;
    errorMessage.value = '';

    try {
        units.value = await apiRequest('/units', { token: props.token });
    } catch (error) {
        if (error.status === 401) {
            emit('unauthorized');
            return;
        }

        errorMessage.value = getErrorMessage(error);
    } finally {
        isLoading.value = false;
    }
};

const saveUnit = async () => {
    isSaving.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    const payload = {
        code: form.code.trim().toUpperCase(),
        name: form.name.trim(),
        precision: Number(form.precision),
    };

    try {
        const data = await apiRequest(isEditing.value ? `/units/${form.id}` : '/units', {
            method: isEditing.value ? 'PUT' : 'POST',
            token: props.token,
            body: payload,
        });

        successMessage.value = data?.message || (isEditing.value ? 'Cập nhật đơn vị thành công' : 'Tạo đơn vị thành công');
        resetForm();
        await loadUnits();
    } catch (error) {
        if (error.status === 401) {
            emit('unauthorized');
            return;
        }

        errorMessage.value = getErrorMessage(error);
    } finally {
        isSaving.value = false;
    }
};

const editUnit = (unit) => {
    form.id = unit.id;
    form.code = unit.code;
    form.name = unit.name;
    form.precision = unit.precision;
    successMessage.value = '';
    errorMessage.value = '';
};

const deleteUnit = async (unit) => {
    if (!window.confirm(`Xóa đơn vị ${unit.code}?`)) {
        return;
    }

    isSaving.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const data = await apiRequest(`/units/${unit.id}`, {
            method: 'DELETE',
            token: props.token,
        });

        successMessage.value = data?.message || 'Xóa đơn vị thành công';
        resetForm();
        await loadUnits();
    } catch (error) {
        if (error.status === 401) {
            emit('unauthorized');
            return;
        }

        errorMessage.value = getErrorMessage(error);
    } finally {
        isSaving.value = false;
    }
};

onMounted(loadUnits);

watch(() => props.token, loadUnits);
</script>

<template>
    <main class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-theme-sm text-gray-500 dark:text-gray-400">Inventory settings</p>
                <h1 class="mt-1 text-title-sm font-semibold text-gray-800 dark:text-white/90">Units</h1>
            </div>
            <nav class="flex items-center gap-2 text-theme-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                <a class="hover:text-brand-500" href="#dashboard">Home</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-white/90">Units</span>
            </nav>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article
                v-for="card in summaryCards"
                :key="card.label"
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900"
            >
                <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ card.label }}</p>
                <div class="mt-4 flex items-end justify-between gap-4">
                    <p class="text-[32px] font-semibold leading-10 text-gray-900 dark:text-white">{{ card.value }}</p>
                    <span class="rounded-full bg-brand-50 px-2.5 py-1 text-theme-xs font-medium text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                        {{ card.note }}
                    </span>
                </div>
            </article>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_380px]">
            <section class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 p-5 dark:border-gray-800 md:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Danh sách đơn vị</h2>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                                Dữ liệu được tải trực tiếp từ API <span class="font-medium text-gray-700 dark:text-gray-300">/api/units</span>.
                            </p>
                        </div>

                        <label class="flex h-11 min-w-[240px] items-center gap-3 rounded-lg border border-gray-300 bg-transparent px-4 text-gray-500 shadow-theme-xs dark:border-gray-700 dark:text-gray-400">
                            <svg class="size-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path
                                    fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 1 0 3.47 9.77l2.63 2.63a.75.75 0 0 0 1.06-1.06l-2.63-2.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <input
                                v-model="search"
                                class="w-full bg-transparent text-theme-sm text-gray-800 outline-none placeholder:text-gray-400 dark:text-white/90"
                                placeholder="Tìm đơn vị..."
                                type="search"
                            />
                        </label>
                    </div>
                </div>

                <div v-if="errorMessage" class="mx-5 mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-theme-sm text-red-600 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                    {{ errorMessage }}
                </div>

                <div v-if="successMessage" class="mx-5 mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-theme-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
                    {{ successMessage }}
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px]">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-left dark:border-gray-800 dark:bg-white/[0.02]">
                                <th class="px-5 py-3 text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">Mã</th>
                                <th class="px-5 py-3 text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tên đơn vị</th>
                                <th class="px-5 py-3 text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">Làm tròn</th>
                                <th class="px-5 py-3 text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">Cập nhật</th>
                                <th class="px-5 py-3 text-right text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <tr v-if="isLoading">
                                <td class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400" colspan="5">
                                    Đang tải dữ liệu...
                                </td>
                            </tr>
                            <tr v-else-if="filteredUnits.length === 0">
                                <td class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400" colspan="5">
                                    Chưa có đơn vị phù hợp.
                                </td>
                            </tr>
                            <template v-else>
                                <tr v-for="unit in filteredUnits" :key="unit.id" class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ unit.code }}</td>
                                    <td class="px-5 py-4">
                                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ unit.name }}</p>
                                        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">ID: {{ unit.id }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ unit.precision }} chữ số</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">
                                        {{ unit.updated_at ? new Date(unit.updated_at).toLocaleDateString('vi-VN') : '-' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="grid size-9 place-items-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5"
                                                type="button"
                                                aria-label="Edit unit"
                                                @click="editUnit(unit)"
                                            >
                                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M13.59 3.59a2 2 0 0 1 2.82 2.82l-.88.88-2.82-2.82.88-.88ZM11.65 5.53 4 13.17V16h2.83l7.64-7.65-2.82-2.82Z" />
                                                </svg>
                                            </button>
                                            <button
                                                class="grid size-9 place-items-center rounded-lg text-red-500 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10"
                                                type="button"
                                                aria-label="Delete unit"
                                                @click="deleteUnit(unit)"
                                            >
                                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M8.75 2a1.75 1.75 0 0 0-1.73 1.5H4.75a.75.75 0 0 0 0 1.5h10.5a.75.75 0 0 0 0-1.5h-2.27A1.75 1.75 0 0 0 11.25 2h-2.5ZM6 6.5h8l-.48 9.13A2.5 2.5 0 0 1 11.02 18H8.98a2.5 2.5 0 0 1-2.5-2.37L6 6.5Z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 md:p-6">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ isEditing ? 'Cập nhật đơn vị' : 'Tạo đơn vị' }}
                    </h2>
                    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                        Form này gọi trực tiếp API backend bạn đã viết.
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="saveUnit">
                    <div>
                        <label class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-300" for="unit-name">
                            Tên đơn vị
                        </label>
                        <input
                            id="unit-name"
                            v-model="form.name"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="Ví dụ: Gram"
                            type="text"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-300" for="unit-code">
                                Mã
                            </label>
                            <input
                                id="unit-code"
                                v-model="form.code"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm uppercase text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                placeholder="G"
                                type="text"
                            />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-300" for="unit-precision">
                                Làm tròn
                            </label>
                            <input
                                id="unit-precision"
                                v-model.number="form.precision"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                max="6"
                                min="0"
                                type="number"
                            />
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            class="inline-flex h-11 flex-1 items-center justify-center rounded-lg bg-brand-500 px-4 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-70"
                            type="submit"
                            :disabled="isSaving"
                        >
                            {{ isSaving ? 'Đang lưu...' : isEditing ? 'Cập nhật' : 'Lưu đơn vị' }}
                        </button>
                        <button
                            class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                            type="button"
                            @click="resetForm"
                        >
                            Hủy
                        </button>
                    </div>
                </form>
            </aside>
        </div>
    </main>
</template>
