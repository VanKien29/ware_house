<template>
    <main class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-theme-sm text-gray-500 dark:text-gray-400">Dashboard</p>
                <h1 class="mt-1 text-title-sm font-semibold text-gray-800 dark:text-white/90">Blank Page</h1>
            </div>
            <nav class="flex items-center gap-2 text-theme-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                <a class="hover:text-brand-500" href="#" @click.prevent>Home</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-white/90">Blank</span>
            </nav>
        </div>

        <section class="min-h-[calc(100vh-190px)] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 md:p-6">
            <div class="h-full min-h-[420px] rounded-xl border border-dashed border-gray-300 bg-gray-25 dark:border-gray-700 dark:bg-white/[0.02]"></div>
        </section>
    </main>
</template>
