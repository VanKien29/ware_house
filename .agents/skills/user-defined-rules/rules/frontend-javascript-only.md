# Frontend JavaScript Only

## Mo ta

Du an frontend nay uu tien JavaScript thuan de chu du an de doc va tu sua.

## Quy tac

- Khong tao file `.ts` cho frontend neu nguoi dung khong yeu cau ro.
- Khong dung `<script setup lang="ts">` trong Vue component moi.
- Dung `<script setup>` va file `.js` cho constants, composables, helpers.
- Neu template tham chieu co TypeScript, chuyen sang JavaScript truoc khi tich hop vao du an.

## Vi du

### Dung

```vue
<script setup>
import { ref } from 'vue';

const isOpen = ref(false);
</script>
```

### Sai

```vue
<script setup lang="ts">
const isOpen = ref<boolean>(false);
</script>
```

## Nguon goc

- Ngay tao: 2026-05-25
- Ly do: Nguoi dung noi "toi chi biet js thoi", nen frontend can giu JavaScript thuan.
