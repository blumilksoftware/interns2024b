import {type VisitOptions, type Method} from '@inertiajs/core/types/types'

export interface VisitPayload extends Partial<VisitOptions>{
    method: Method
    data?: Record<string, any>
}
