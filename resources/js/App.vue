<script setup>
const menuItems = ['Dashboard', 'Products', 'Orders', 'Customers'];
const stats = [
    { label: 'Products', value: 0 },
    { label: 'Orders', value: 0 },
    { label: 'Customers', value: 0 },
];
</script>

<template>
    <div class="min-h-screen bg-zinc-50 text-zinc-900">
        <aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-zinc-200 bg-white px-5 py-6 md:block">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Laravel Vue Base</p>
                <h1 class="mt-1 text-xl font-bold">Management App</h1>
            </div>

            <nav class="mt-8 space-y-1">
                <a
                    v-for="item in menuItems"
                    :key="item"
                    href="#"
                    class="block rounded-md px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-emerald-50 hover:text-emerald-700"
                >
                    {{ item }}
                </a>
            </nav>
        </aside>

        <main class="md:pl-64">
            <header class="border-b border-zinc-200 bg-white px-5 py-4">
                <div class="mx-auto flex max-w-6xl items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-500">Starter project</p>
                        <h2 class="text-2xl font-bold">Dashboard</h2>
                    </div>

                    <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        New Item
                    </button>
                </div>
            </header>

            <section class="mx-auto max-w-6xl px-5 py-6">
                <div class="grid gap-4 sm:grid-cols-3">
                    <article
                        v-for="stat in stats"
                        :key="stat.label"
                        class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm"
                    >
                        <p class="text-sm text-zinc-500">{{ stat.label }}</p>
                        <p class="mt-2 text-3xl font-bold">{{ stat.value }}</p>
                    </article>
                </div>

                <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold">Ready for your first CRUD module</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-600">
                        Start by creating a migration, model, controller, and routes for one simple module.
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
