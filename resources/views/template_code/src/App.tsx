import React, { useState } from 'react';
import Navbar from './components/Navbar';
import HomePage from './pages/HomePage';
import ExplorePage from './pages/ExplorePage';
import DetailPage from './pages/DetailPage';
import VendorDashboard from './pages/VendorDashboard';
import ProfilePage from './pages/ProfilePage';
import AddSpotPage from './pages/AddSpotPage';
import { FOOD_PLACES } from './data';

export default function App() {
  const [currentPage, setCurrentPage] = useState('home');
  const [selectedPlace, setSelectedPlace] = useState(null);
  const [places, setPlaces] = useState(FOOD_PLACES);
  const [userRole, setUserRole] = useState<'diner' | 'vendor'>('diner');

  const toggleRole = () => {
    if (userRole === 'diner') {
      setUserRole('vendor');
      navigateTo('vendor');
    } else {
      setUserRole('diner');
      navigateTo('home');
    }
  };

  const navigateTo = (page: string, data: any = null) => {
    setCurrentPage(page);
    if (data) setSelectedPlace(data);
  };

  const handleAddSpot = (newSpot: any) => {
    setPlaces([newSpot, ...places]);
    navigateTo('explore');
  };

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      <Navbar currentPage={currentPage} navigateTo={navigateTo} userRole={userRole} toggleRole={toggleRole} />
      <main className="flex-grow">
        {currentPage === 'home' && <HomePage navigateTo={navigateTo} places={places} />}
        {currentPage === 'explore' && <ExplorePage navigateTo={navigateTo} places={places} />}
        {currentPage === 'detail' && <DetailPage place={selectedPlace} navigateTo={navigateTo} />}
        {currentPage === 'vendor' && <VendorDashboard navigateTo={navigateTo} />}
        {currentPage === 'profile' && <ProfilePage navigateTo={navigateTo} places={places} />}
        {currentPage === 'add-spot' && <AddSpotPage onAddSpot={handleAddSpot} navigateTo={navigateTo} />}
      </main>
    </div>
  );
}
