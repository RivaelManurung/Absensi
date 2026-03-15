import { useMutation, useQueryClient } from "@tanstack/react-query";
import { useState } from "react";
import { Link } from "react-router-dom";
import { useToast } from "../../../app/providers/ToastProvider";
import { getApiErrorMessage } from "../../../lib/apiError";
import { Button } from "../../../components/ui/button";
import { APP_ROUTES } from "../../../lib/constants";
import { UserFormSection } from "../components/UserFormSection";
import { userService } from "../services/userService";

export function UsersCreatePage() {
  const [form, setForm] = useState({ name: "", email: "", password: "", isActive: true });
  const queryClient = useQueryClient();
  const toast = useToast();
  const createMutation = useMutation({
    mutationFn: () =>
      userService.createUser({
        name: form.name,
        email: form.email,
        password: form.password,
        is_active: form.isActive,
      }),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["users"] });
      toast.success("User berhasil dibuat.");
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal membuat user."));
    },
  });

  return (
    <section className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold">Create User</h1>
      </header>

      <form
        className="grid gap-4 rounded-2xl border border-border bg-card p-5"
        onSubmit={(event) => {
          event.preventDefault();
          createMutation.mutate();
        }}
      >
        <UserFormSection
          name={form.name}
          email={form.email}
          password={form.password}
          isActive={form.isActive}
          onChange={(next) => setForm((previous) => ({ ...previous, ...next }))}
        />

        <div className="flex flex-wrap gap-3">
          <Button type="submit" disabled={createMutation.isPending}>Create User</Button>
          <Link to={APP_ROUTES.users}><Button variant="outline" type="button">Cancel</Button></Link>
        </div>
      </form>
    </section>
  );
}
