import React, { useState } from 'react';
import { Search, Map, User, Store, Menu, X, UtensilsCrossed, PlusCircle, ArrowRightLeft, LayoutDashboard } from 'lucide-react';

export default function Navbar({ currentPage, navigateTo, userRole, toggleRole }: { currentPage: string, navigateTo: (page: string) => void, userRole: string, toggleRole: () => void }) {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const dinerNav = [
    { id: 'home', label: 'Home', icon: <Search size={20} /> },
    { id: 'explore', label: 'Explore', icon: <Map size={20} /> },
    { id: 'profile', label: 'Profile', icon: <User size={20} /> },
  ];

  const vendorNav = [
    { id: 'vendor', label: 'Dashboard', icon: <LayoutDashboard size={20} /> },
    { id: 'profile', label: 'Profile', icon: <User size={20} /> },
  ];

  const navItems = userRole === 'vendor' ? vendorNav : dinerNav;

  return (
    <nav className="bg-white border-b border-gray-100 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex items-center cursor-pointer" onClick={() => navigateTo('home')}>
            <UtensilsCrossed className="h-8 w-8 text-primary" />
            <span className="ml-2 text-xl font-bold text-charcoal tracking-tight">BiteSpot</span>
          </div>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-8">
            {navItems.map((item) => (
              <button
                key={item.id}
                onClick={() => navigateTo(item.id)}
                className={`flex items-center space-x-1 px-3 py-2 rounded-md text-sm font-medium transition-colors ${
                  currentPage === item.id
                    ? 'text-primary bg-cream'
                    : 'text-gray-600 hover:text-primary hover:bg-gray-50'
                }`}
              >
                {item.icon}
                <span>{item.label}</span>
              </button>
            ))}
            
            {/* Role Toggle */}
            <div className="flex items-center ml-4 pl-6 border-l border-gray-200">
              <button
                onClick={toggleRole}
                className="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-charcoal px-4 py-2 rounded-full text-sm font-medium transition-colors"
              >
                <ArrowRightLeft size={16} className="text-gray-500" />
                <span>View as {userRole === 'diner' ? 'Vendor' : 'Diner'}</span>
              </button>
            </div>
          </div>

          {/* Mobile menu button */}
          <div className="flex items-center md:hidden">
            <button
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              className="text-gray-600 hover:text-primary focus:outline-none"
            >
              {isMobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Navigation */}
      {isMobileMenuOpen && (
        <div className="md:hidden bg-white border-t border-gray-100">
          <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            {navItems.map((item) => (
              <button
                key={item.id}
                onClick={() => {
                  navigateTo(item.id);
                  setIsMobileMenuOpen(false);
                }}
                className={`flex items-center w-full space-x-3 px-3 py-3 rounded-md text-base font-medium ${
                  currentPage === item.id
                    ? 'text-primary bg-cream'
                    : 'text-gray-600 hover:text-primary hover:bg-gray-50'
                }`}
              >
                {item.icon}
                <span>{item.label}</span>
              </button>
            ))}
            
            <div className="pt-4 mt-2 border-t border-gray-100">
              <button
                onClick={() => {
                  toggleRole();
                  setIsMobileMenuOpen(false);
                }}
                className="flex items-center w-full space-x-3 px-3 py-3 rounded-md text-base font-medium text-charcoal bg-gray-100 hover:bg-gray-200 transition-colors"
              >
                <ArrowRightLeft size={20} className="text-gray-500" />
                <span>Switch to {userRole === 'diner' ? 'Vendor' : 'Diner'} View</span>
              </button>
            </div>
          </div>
        </div>
      )}
    </nav>
  );
}
