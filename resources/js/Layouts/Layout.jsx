import {Link} from "@inertiajs/react";

export default function Layout({children}) {
    return (
        <>
            <header>
                <nav className="flex gap-2">
                    <Link className="nav-link" href="/">
                        Home
                    </Link>
                    <Link className="nav-link" href="/customer/login">
                        Login
                    </Link>
                </nav>
            </header>
            <main>{children}</main>
        </>
    );
}
