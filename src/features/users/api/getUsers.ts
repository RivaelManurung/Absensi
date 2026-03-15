import { userService } from "../services/userService";
import type { UserListParams } from "../types/user.types";

export async function getUsers(params: UserListParams = {}) {
  return userService.getUsers(params);
}
