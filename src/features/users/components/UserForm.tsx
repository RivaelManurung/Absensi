import { useState } from "react";
import { Button } from "../../../components/ui/button";
import { Input } from "../../../components/ui/input";

export function UserForm() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");

  return (
    <form className="grid gap-3">
      <Input placeholder="Full name" value={name} onChange={(event) => setName(event.target.value)} />
      <Input placeholder="Email" value={email} onChange={(event) => setEmail(event.target.value)} />
      <Button type="button">Save User</Button>
    </form>
  );
}
