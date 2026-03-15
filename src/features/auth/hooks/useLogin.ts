import { useMutation } from "@tanstack/react-query";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../../app/providers/AuthProvider";
import { useToast } from "../../../app/providers/ToastProvider";
import { getApiErrorMessage } from "../../../lib/apiError";
import { APP_ROUTES } from "../../../lib/constants";

export function useLogin() {
  const navigate = useNavigate();
  const { login } = useAuth();
  const toast = useToast();

  return useMutation({
    mutationFn: login,
    onSuccess: () => navigate(APP_ROUTES.home),
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Login gagal."));
    },
  });
}
