import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function AuthenticatedLayout({ header, children }) {
    const { auth } = usePage().props;
    const user = auth?.user || null;

    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="border-b border-gray-100 bg-white">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex h-16 justify-between">
                        {/* Logo */}
                        <div className="flex">
                            <div className="flex shrink-0 items-center">
                                <Link href="/">
                                    <img src="https://png.pngtree.com/png-clipart/20230120/original/pngtree-car-logo-design-png-image_8923406.png" alt="" className="block h-9 w-12 fill-current text-gray-800"/>
                                </Link>
                            </div>
                        </div>


                        {/* Desktop Navigation */}
                        <div className="hidden sm:flex sm:items-center">
                            {user ? (
                                <div className="relative">
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <span className="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                                >
                                                    {user.name}
                                                    <svg
                                                        className="-me-0.5 ms-2 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </Dropdown.Trigger>
                                        <Dropdown.Content>
                                            <Dropdown.Link href={route('rental.history')}>
                                                History
                                            </Dropdown.Link>
                                            <Dropdown.Link href={route('profile.edit')}>
                                                Profile
                                            </Dropdown.Link>
                                            <Dropdown.Link href={route('logout')} method="post" as="button">
                                                Log Out
                                            </Dropdown.Link>
                                        </Dropdown.Content>
                                    </Dropdown>
                                </div>
                            ) : (
                                <div className="flex space-x-4">
                                    <Link
                                        href={route('register')}
                                        className="text-sm font-medium text-gray-600 hover:text-gray-800"
                                    >
                                        Register
                                    </Link>
                                    <Link
                                        href={route('login')}
                                        className="text-sm font-medium text-gray-600 hover:text-gray-800"
                                    >
                                        Login
                                    </Link>
                                </div>
                            )}
                        </div>

                        {/* Mobile Menu Button */}
                        <div className="-me-2 flex items-center sm:hidden">
                            <button
                                onClick={() => setShowingNavigationDropdown(!showingNavigationDropdown)}
                                className="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    className="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    {showingNavigationDropdown ? (
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                    ) : (
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                    )}
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {/* Mobile Dropdown */}
                {showingNavigationDropdown && (
                    <div className="sm:hidden">
                        <div className="space-y-1 pb-3 pt-2">
                            {user ? (
                                <>
                                    <ResponsiveNavLink href={route('rental.history')}>
                                        History
                                    </ResponsiveNavLink>
                                    <ResponsiveNavLink href={route('profile.edit')}>
                                        Profile
                                    </ResponsiveNavLink>
                                    <ResponsiveNavLink method="post" href={route('logout')} as="button">
                                        Log Out
                                    </ResponsiveNavLink>
                                </>
                            ) : (
                                <>
                                    <ResponsiveNavLink href={route('login')}>
                                        Login
                                    </ResponsiveNavLink>
                                    <ResponsiveNavLink href={route('register')}>
                                        Register
                                    </ResponsiveNavLink>
                                </>
                            )}
                        </div>
                    </div>
                )}
            </nav>

            {header && (
                <header className="bg-white shadow">
                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}

