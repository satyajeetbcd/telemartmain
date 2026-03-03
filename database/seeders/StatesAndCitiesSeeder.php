<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatesAndCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Indian States and Cities...');

        // First, try to fetch from API
        $apiData = $this->fetchFromAPI();
        
        if ($apiData) {
            $this->seedFromAPI($apiData);
        } else {
            // Fallback to hardcoded data
            $this->command->info('API fetch failed, using hardcoded data...');
            $this->seedHardcodedData();
        }

        $this->command->info('States and Cities seeded successfully!');
    }

    /**
     * Try to fetch data from Country State City API
     */
    private function fetchFromAPI(): ?array
    {
        try {
            // Using Country State City API (free tier)
            $response = Http::timeout(30)->get('https://api.countrystatecity.in/v1/countries/IN/states', [
                'X-CSCAPI-KEY' => env('CSC_API_KEY', '') // Optional API key
            ]);

            if ($response->successful()) {
                $states = $response->json();
                
                $data = [];
                foreach ($states as $state) {
                    $stateCode = $state['iso2'] ?? null;
                    $stateName = $state['name'] ?? null;
                    
                    if ($stateName) {
                        // Fetch cities for this state
                        $citiesResponse = Http::timeout(30)->get("https://api.countrystatecity.in/v1/countries/IN/states/{$stateCode}/cities", [
                            'X-CSCAPI-KEY' => env('CSC_API_KEY', '')
                        ]);
                        
                        $cities = [];
                        if ($citiesResponse->successful()) {
                            $citiesData = $citiesResponse->json();
                            foreach ($citiesData as $city) {
                                $cities[] = $city['name'] ?? null;
                            }
                        }
                        
                        $data[] = [
                            'name' => $stateName,
                            'code' => $stateCode,
                            'cities' => array_filter($cities)
                        ];
                    }
                }
                
                return $data;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch states/cities from API: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Seed data from API response
     */
    private function seedFromAPI(array $apiData): void
    {
        foreach ($apiData as $stateData) {
            $state = State::firstOrCreate(
                ['name' => $stateData['name']],
                ['code' => $stateData['code'], 'is_active' => true]
            );
            
            foreach ($stateData['cities'] as $cityName) {
                City::firstOrCreate(
                    [
                        'state_id' => $state->id,
                        'name' => $cityName
                    ],
                    ['is_active' => true]
                );
            }
            
            $this->command->info("Seeded state: {$stateData['name']} with " . count($stateData['cities']) . " cities");
        }
    }

    /**
     * Seed hardcoded comprehensive data for all Indian states and major cities
     */
    private function seedHardcodedData(): void
    {
        $statesData = [
            ['name' => 'Andhra Pradesh', 'code' => 'AP', 'cities' => ['Hyderabad', 'Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Rajahmundry', 'Kakinada', 'Tirupati', 'Anantapur', 'Kadapa', 'Vizianagaram', 'Eluru', 'Ongole', 'Nandyal', 'Machilipatnam', 'Adoni', 'Tenali', 'Chittoor', 'Hindupur']],
            ['name' => 'Arunachal Pradesh', 'code' => 'AR', 'cities' => ['Itanagar', 'Naharlagun', 'Pasighat', 'Tawang', 'Ziro', 'Bomdila', 'Tezu', 'Daporijo', 'Namsai', 'Along']],
            ['name' => 'Assam', 'code' => 'AS', 'cities' => ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon', 'Tinsukia', 'Tezpur', 'Bongaigaon', 'Dhubri', 'Diphu', 'North Lakhimpur', 'Karimganj', 'Goalpara', 'Sivasagar', 'Barpeta', 'Golaghat', 'Hailakandi', 'Mangaldoi', 'Hojai', 'Morigaon']],
            ['name' => 'Bihar', 'code' => 'BR', 'cities' => ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Purnia', 'Darbhanga', 'Bihar Sharif', 'Arrah', 'Begusarai', 'Katihar', 'Munger', 'Chapra', 'Hajipur', 'Sitamarhi', 'Saharsa', 'Dehri', 'Bettiah', 'Motihari', 'Siwan', 'Kishanganj']],
            ['name' => 'Chhattisgarh', 'code' => 'CG', 'cities' => ['Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Durg', 'Rajnandgaon', 'Raigarh', 'Jagdalpur', 'Ambikapur', 'Dhamtari', 'Chirmiri', 'Mahasamund', 'Dalli-Rajhara', 'Kanker', 'Kawardha', 'Janjgir', 'Khairagarh', 'Mungeli', 'Baloda Bazar', 'Bhatapara']],
            ['name' => 'Goa', 'code' => 'GA', 'cities' => ['Panaji', 'Vasco da Gama', 'Margao', 'Mapusa', 'Ponda', 'Mormugao', 'Bicholim', 'Curchorem', 'Valpoi', 'Canacona']],
            ['name' => 'Gujarat', 'code' => 'GJ', 'cities' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar', 'Gandhinagar', 'Junagadh', 'Gandhidham', 'Anand', 'Navsari', 'Surendranagar', 'Bharuch', 'Mehsana', 'Bhuj', 'Porbandar', 'Palanpur', 'Valsad', 'Vapi', 'Godhra']],
            ['name' => 'Haryana', 'code' => 'HR', 'cities' => ['Faridabad', 'Gurgaon', 'Panipat', 'Ambala', 'Yamunanagar', 'Rohtak', 'Hisar', 'Karnal', 'Sonipat', 'Panchkula', 'Sirsa', 'Bhiwani', 'Bahadurgarh', 'Jind', 'Thanesar', 'Kaithal', 'Rewari', 'Palwal', 'Hansi', 'Narnaul']],
            ['name' => 'Himachal Pradesh', 'code' => 'HP', 'cities' => ['Shimla', 'Mandi', 'Solan', 'Dharamshala', 'Bilaspur', 'Kangra', 'Hamirpur', 'Una', 'Chamba', 'Nahan', 'Palampur', 'Kullu', 'Manali', 'Kasauli', 'Dalhousie']],
            ['name' => 'Jharkhand', 'code' => 'JH', 'cities' => ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro Steel City', 'Hazaribagh', 'Deoghar', 'Giridih', 'Phusro', 'Adityapur', 'Chatra', 'Gumla', 'Ramgarh', 'Jhumri Telaiya', 'Sahibganj', 'Medininagar', 'Chaibasa', 'Jhumri Tilaiya', 'Lohardaga', 'Pakur', 'Simdega']],
            ['name' => 'Karnataka', 'code' => 'KA', 'cities' => ['Bangalore', 'Mysore', 'Hubli', 'Mangalore', 'Belgaum', 'Gulbarga', 'Davangere', 'Bellary', 'Bijapur', 'Shimoga', 'Tumkur', 'Raichur', 'Bidar', 'Hospet', 'Hassan', 'Mandya', 'Chitradurga', 'Udupi', 'Gadag', 'Bagalkot']],
            ['name' => 'Kerala', 'code' => 'KL', 'cities' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam', 'Alappuzha', 'Palakkad', 'Kannur', 'Kottayam', 'Malappuram', 'Manjeri', 'Thalassery', 'Varkala', 'Pathanamthitta', 'Kasaragod', 'Idukki', 'Wayanad', 'Ernakulam', 'Muvattupuzha', 'Chalakudy']],
            ['name' => 'Madhya Pradesh', 'code' => 'MP', 'cities' => ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain', 'Sagar', 'Ratlam', 'Satna', 'Rewa', 'Murwara', 'Singrauli', 'Burhanpur', 'Khandwa', 'Morena', 'Bhind', 'Chhindwara', 'Guna', 'Shivpuri', 'Vidisha', 'Chhatarpur']],
            ['name' => 'Maharashtra', 'code' => 'MH', 'cities' => ['Mumbai', 'Pune', 'Nagpur', 'Thane', 'Nashik', 'Aurangabad', 'Solapur', 'Amravati', 'Nanded', 'Kolhapur', 'Sangli', 'Jalgaon', 'Akola', 'Latur', 'Ahmednagar', 'Chandrapur', 'Parbhani', 'Ichalkaranji', 'Jalna', 'Bhusawal']],
            ['name' => 'Manipur', 'code' => 'MN', 'cities' => ['Imphal', 'Thoubal', 'Kakching', 'Lilong', 'Mayang Imphal', 'Yairipok', 'Moirang', 'Nambol', 'Oinam', 'Wangjing']],
            ['name' => 'Meghalaya', 'code' => 'ML', 'cities' => ['Shillong', 'Tura', 'Jowai', 'Nongstoin', 'Williamnagar', 'Baghmara', 'Resubelpara', 'Mairang', 'Mawkyrwat', 'Ampati']],
            ['name' => 'Mizoram', 'code' => 'MZ', 'cities' => ['Aizawl', 'Lunglei', 'Saiha', 'Champhai', 'Kolasib', 'Serchhip', 'Lawngtlai', 'Mamit', 'Khawzawl', 'Hnahthial']],
            ['name' => 'Nagaland', 'code' => 'NL', 'cities' => ['Kohima', 'Dimapur', 'Mokokchung', 'Tuensang', 'Wokha', 'Zunheboto', 'Mon', 'Phek', 'Kiphire', 'Longleng']],
            ['name' => 'Odisha', 'code' => 'OD', 'cities' => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur', 'Puri', 'Baleshwar', 'Bhadrak', 'Baripada', 'Balangir', 'Jharsuguda', 'Bargarh', 'Paradip', 'Bhawanipatna', 'Dhenkanal', 'Barbil', 'Kendujhar', 'Rayagada', 'Jeypore', 'Phulbani']],
            ['name' => 'Punjab', 'code' => 'PB', 'cities' => ['Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Pathankot', 'Hoshiarpur', 'Batala', 'Moga', 'Abohar', 'Malerkotla', 'Khanna', 'Mohali', 'Barnala', 'Firozpur', 'Kapurthala', 'Muktsar', 'Faridkot', 'Sangrur', 'Fazilka']],
            ['name' => 'Rajasthan', 'code' => 'RJ', 'cities' => ['Jaipur', 'Jodhpur', 'Kota', 'Bikaner', 'Ajmer', 'Udaipur', 'Bhilwara', 'Alwar', 'Bharatpur', 'Sikar', 'Pali', 'Tonk', 'Baran', 'Dausa', 'Churu', 'Banswara', 'Hanumangarh', 'Dholpur', 'Barmer', 'Jhunjhunu']],
            ['name' => 'Sikkim', 'code' => 'SK', 'cities' => ['Gangtok', 'Namchi', 'Mangan', 'Gyalshing', 'Singtam', 'Rangpo', 'Jorethang', 'Ravangla', 'Pakyong', 'Soreng']],
            ['name' => 'Tamil Nadu', 'code' => 'TN', 'cities' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli', 'Erode', 'Vellore', 'Dindigul', 'Thanjavur', 'Tuticorin', 'Kanchipuram', 'Nagercoil', 'Kumbakonam', 'Cuddalore', 'Karaikudi', 'Neyveli', 'Pollachi', 'Rajapalayam', 'Hosur']],
            ['name' => 'Telangana', 'code' => 'TS', 'cities' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Ramagundam', 'Khammam', 'Mahbubnagar', 'Nalgonda', 'Adilabad', 'Siddipet', 'Suryapet', 'Miryalaguda', 'Jagtial', 'Mancherial', 'Peddapalli', 'Kamareddy', 'Sangareddy', 'Wanaparthy', 'Narayanpet', 'Medak']],
            ['name' => 'Tripura', 'code' => 'TR', 'cities' => ['Agartala', 'Udaipur', 'Dharmanagar', 'Kailasahar', 'Belonia', 'Khowai', 'Teliamura', 'Ambassa', 'Sabroom', 'Kumarghat']],
            ['name' => 'Uttar Pradesh', 'code' => 'UP', 'cities' => ['Lucknow', 'Kanpur', 'Agra', 'Meerut', 'Varanasi', 'Allahabad', 'Bareilly', 'Ghaziabad', 'Aligarh', 'Moradabad', 'Saharanpur', 'Gorakhpur', 'Noida', 'Firozabad', 'Jhansi', 'Muzaffarnagar', 'Mathura', 'Rampur', 'Shahjahanpur', 'Farrukhabad']],
            ['name' => 'Uttarakhand', 'code' => 'UK', 'cities' => ['Dehradun', 'Haridwar', 'Roorkee', 'Haldwani', 'Rudrapur', 'Kashipur', 'Rishikesh', 'Ramnagar', 'Pithoragarh', 'Almora', 'Nainital', 'Mussoorie', 'Pauri', 'New Tehri', 'Kotdwara', 'Srinagar', 'Chamoli Gopeshwar', 'Bageshwar', 'Champawat', 'Uttarkashi']],
            ['name' => 'West Bengal', 'code' => 'WB', 'cities' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri', 'Bardhaman', 'Malda', 'Kharagpur', 'Jalpaiguri', 'Baharampur', 'Krishnanagar', 'Raiganj', 'Haldia', 'Santipur', 'Dankuni', 'Balurghat', 'Habra', 'Kalyani', 'Medinipur', 'Bankura']],
            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN', 'cities' => ['Port Blair', 'Diglipur', 'Mayabunder', 'Rangat', 'Car Nicobar', 'Hut Bay', 'Bamboo Flat', 'Garacharma', 'Ferrargunj', 'Bakultala']],
            ['name' => 'Chandigarh', 'code' => 'CH', 'cities' => ['Chandigarh', 'Manimajra', 'Burail', 'Daria', 'Kishangarh', 'Mauli Jagran', 'Sector 17', 'Sector 22', 'Sector 35', 'Sector 43']],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DH', 'cities' => ['Daman', 'Diu', 'Silvassa', 'Naroli', 'Vapi', 'Dadra', 'Amli', 'Khanvel', 'Masat', 'Rakholi']],
            ['name' => 'Delhi', 'code' => 'DL', 'cities' => ['New Delhi', 'Delhi', 'Noida', 'Gurgaon', 'Faridabad', 'Ghaziabad', 'Greater Noida', 'Sahibabad', 'Rohini', 'Dwarka', 'Pitampura', 'Laxmi Nagar', 'Karol Bagh', 'Connaught Place', 'Rajouri Garden', 'Janakpuri', 'Rohini', 'Patel Nagar', 'Paharganj', 'Civil Lines']],
            ['name' => 'Jammu and Kashmir', 'code' => 'JK', 'cities' => ['Srinagar', 'Jammu', 'Anantnag', 'Baramulla', 'Sopore', 'Kathua', 'Udhampur', 'Rajouri', 'Poonch', 'Doda', 'Kishtwar', 'Ramban', 'Reasi', 'Samba', 'Ganderbal', 'Bandipora', 'Kupwara', 'Pulwama', 'Shopian', 'Kulgam']],
            ['name' => 'Ladakh', 'code' => 'LA', 'cities' => ['Leh', 'Kargil', 'Drass', 'Nubra', 'Zanskar', 'Nyoma', 'Diskit', 'Hemis', 'Alchi', 'Thiksey']],
            ['name' => 'Lakshadweep', 'code' => 'LD', 'cities' => ['Kavaratti', 'Agatti', 'Amini', 'Andrott', 'Bitra', 'Chettlat', 'Kadmat', 'Kalpeni', 'Kiltan', 'Minicoy']],
            ['name' => 'Puducherry', 'code' => 'PY', 'cities' => ['Puducherry', 'Karaikal', 'Mahe', 'Yanam', 'Ozhukarai', 'Villianur', 'Bahour', 'Nettapakkam', 'Ariyankuppam', 'Muthialpet']],
        ];

        foreach ($statesData as $stateData) {
            $state = State::firstOrCreate(
                ['name' => $stateData['name']],
                ['code' => $stateData['code'], 'is_active' => true]
            );

            foreach ($stateData['cities'] as $cityName) {
                City::firstOrCreate(
                    [
                        'state_id' => $state->id,
                        'name' => $cityName
                    ],
                    ['is_active' => true]
                );
            }

            $this->command->info("Seeded state: {$stateData['name']} with " . count($stateData['cities']) . " cities");
        }
    }
}
