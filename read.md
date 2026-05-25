# Hướng Dẫn Giao Diện TailAdmin Cho AI

File này là tài liệu bắt buộc đọc trước khi sửa UI/frontend của dự án.

## Template Gốc

- Template tham chiếu: `C:\Users\Van Kien\Downloads\vue-tailwind-admin-dashboard-main\vue-tailwind-admin-dashboard-main`
- Template: TailAdmin Vue + Tailwind CSS.
- Dự án hiện tại là Laravel + Vite + Vue 3, mount tại `resources/js/app.js` và `resources/js/App.vue`.
- Frontend dùng JavaScript thuần. Không tạo file `.ts` và không dùng `<script setup lang="ts">` nếu người dùng không yêu cầu.
- CSS theme chính đang nằm tại `resources/css/app.css`.

## Nguyên Tắc Giao Diện

- Giao diện admin phải bám theo TailAdmin: sidebar bên trái, topbar sticky, nền `gray-50`, card/panel màu trắng, border `gray-200`, dark mode dùng `dark:*`.
- Màu chính là brand blue của TailAdmin:
  - `brand-50`: `#ecf3ff`
  - `brand-500`: `#465fff`
  - `brand-600`: `#3641f5`
- Font mặc định là `Outfit` thông qua class/theme `font-outfit`.
- Dùng các token và utility đã có trong `resources/css/app.css`: `text-theme-sm`, `text-theme-xs`, `shadow-theme-xs`, `shadow-theme-sm`, `menu-item`, `menu-item-active`, `menu-item-inactive`, `menu-dropdown-item`.
- Sidebar desktop rộng `290px`; nếu cần chế độ collapsed thì dùng `90px`. Main content canh lề bằng `lg:ml-[290px]` hoặc `lg:ml-[90px]`.
- Header/topbar sticky top, background trắng/dark gray, border bottom `gray-200`/`gray-800`.
- Nội dung trang đặt trong wrapper: `mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6`.
- Panel chính dùng style TailAdmin: `rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900`.

## Cấu Trúc Vue Base Hiện Tại

- `resources/js/App.vue`: chỉ mount shell chính.
- `resources/js/components/layout/AdminShell.vue`: quản lý state layout, mobile sidebar, collapsed sidebar, dark mode và màn hình đang active.
- `resources/js/components/layout/TheSidebar.vue`: sidebar menu TailAdmin.
- `resources/js/components/layout/TheHeader.vue`: topbar/header TailAdmin.
- `resources/js/components/base/BaseSvgIcon.vue`: base icon SVG dùng lại.
- `resources/js/constants/adminNavigation.js`: menu và SVG path cho sidebar.
- `resources/js/views/BlankDashboard.vue`: vùng nội dung trống để nhìn giao diện.
- `resources/js/views/auth/LoginPage.vue`: giao diện login tĩnh.
- `resources/js/views/units/UnitIndex.vue`: giao diện quản lý units, gọi API backend `/api/units`.
- `resources/js/services/apiClient.js`: axios client dùng chung cho API `/api`.
- `resources/js/services/authStorage.js`: lưu/đọc token Sanctum và thông tin user từ `localStorage`.
- Tài khoản seed đang dùng được trong DB hiện tại: `manager@warehouse.test` / `password`.

## Nguyên Tắc Code Vue

- Code Vue mới dùng `<script setup>` JavaScript thuần.
- Constants/composables dùng `.js`, không dùng `.ts`.
- Gọi API dùng axios thông qua `resources/js/services/apiClient.js`, không gọi rải rác trực tiếp trong nhiều component nếu có thể gom lại.
- API sau đăng nhập dùng token Sanctum dạng `Authorization: Bearer <token>`.
- Ưu tiên component nhỏ, rõ trách nhiệm khi màn hình bắt đầu lớn.
- Không copy toàn bộ demo template khi chưa cần; chỉ lấy layout, token, component pattern cần thiết.
- Không thêm dependency nặng của template như charts/calendar/map nếu trang chưa dùng.
- Hiện tại chưa dùng `vue-router`; shell chuyển màn bằng `activeMenu` kết hợp hash nhẹ như `#dashboard`, `#units`, `#login`.
- Khi thêm nav item mới, giữ cùng pattern menu TailAdmin: icon bên trái, label, active state `menu-item-active`.
- Khi thêm nav item mới, thêm `hash` trong `resources/js/constants/adminNavigation.js`, rồi map menu/hash trong `resources/js/components/layout/AdminShell.vue`.

## Nguyên Tắc Tailwind

- Dự án dùng Tailwind CSS v4, không dùng `@tailwind base/components/utilities`.
- Theme khai báo trong CSS bằng `@theme`, không tạo `tailwind.config.js` nếu không bắt buộc.
- Dùng `gap` cho khoảng cách giữa các item trong flex/grid.
- Trang mới phải có dark mode nếu có màu nền, border, text, hover state.
- Tránh palette cũ. UI mới phải đi theo gray + brand blue của TailAdmin.

## Quy Trình Làm Giao Diện Chức Năng

1. Đọc file này.
2. Đọc `resources/css/app.css` để xem token hiện có.
3. Tạo view trong `resources/js/views/<feature>/`.
4. Nếu màn hình dài, tách component con vào `resources/js/components/<feature>/`.
5. Nếu cần API, thêm hàm gọi qua `resources/js/services/apiClient.js` hoặc service riêng trong `resources/js/services/`.
6. Thêm menu hoặc icon vào `resources/js/constants/adminNavigation.js`.
7. Nối màn hình mới trong `resources/js/components/layout/AdminShell.vue`.
8. Chạy `npm run build` để kiểm tra.

## Khi AI Làm Việc Lần Sau

1. Đọc file này.
2. Đọc `resources/css/app.css` để xem token hiện có.
3. Đọc `resources/js/App.vue` hoặc layout/component liên quan trước khi sửa.
4. Nếu cần tham chiếu template gốc, chỉ đọc đúng file cần thiết trong template tại `Downloads`.
5. Giữ giao diện nhất quán với TailAdmin, không quay về style emerald starter cũ.
