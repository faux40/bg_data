import { watchEffect } from 'vue'

export default function useTitle(title) {
  watchEffect(() => {
    if (typeof title === 'string') {
      document.title = title
    } else if (title?.value) {
      document.title = title.value
    }
  })
}