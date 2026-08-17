import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useCan() {
    const page = usePage()

    const can = (permission: string): boolean => {
        const authCan = page.props.auth?.can

        if (!authCan) return false
        if (authCan === '*') return true

        return authCan[permission] === true
    }

    const canAny = (permissions: string[]): boolean => {
        return permissions.some(p => can(p))
    }

    return { can, canAny }
}
