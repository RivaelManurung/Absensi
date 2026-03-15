import { authService } from "../services/authService";
import type { LoginPayload } from "../types/auth.types";

export async function login(payload: LoginPayload) {
  return authService.login(payload);
}
