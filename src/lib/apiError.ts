import { AxiosError } from "axios";

type ApiErrorPayload = {
  message?: string;
  errors?: Record<string, string[]>;
};

export function getApiErrorMessage(error: unknown, fallback: string): string {
  if (error instanceof AxiosError) {
    const payload = error.response?.data as ApiErrorPayload | undefined;

    if (payload?.errors) {
      const firstField = Object.values(payload.errors)[0];
      if (Array.isArray(firstField) && firstField[0]) {
        return firstField[0];
      }
    }

    if (payload?.message) {
      return payload.message;
    }

    if (error.message) {
      return error.message;
    }
  }

  if (error instanceof Error && error.message) {
    return error.message;
  }

  return fallback;
}
