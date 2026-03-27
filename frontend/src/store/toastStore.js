import { create } from 'zustand'

let nextId = 0

export const useToastStore = create((set) => ({
  toasts: [],

  add: ({ type = 'info', title, message, duration = 4000 }) =>
    set((s) => ({
      toasts: [...s.toasts, { id: ++nextId, type, title, message, duration }],
    })),

  remove: (id) =>
    set((s) => ({ toasts: s.toasts.filter((t) => t.id !== id) })),

  success: (message, title) =>
    set((s) => ({
      toasts: [...s.toasts, { id: ++nextId, type: 'success', title, message, duration: 4000 }],
    })),

  error: (message, title = 'Erreur') =>
    set((s) => ({
      toasts: [...s.toasts, { id: ++nextId, type: 'error', title, message, duration: 6000 }],
    })),
}))
