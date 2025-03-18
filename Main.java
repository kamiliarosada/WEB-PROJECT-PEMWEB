public class Main {
    public static void main(String[] args) {
        Node root = new Node(8, 116);
        root.left = new Node(4, 89);
        root.right = new Node(11, 177);
        root.left.right = new Node(3, 109);
        root.right.left = new Node(5, 168);
        root.right.right = new Node(7, 181);
        root.right.left.left = new Node(6, 161);
        root.right.left.left.left = new Node(9, 136);
        root.right.left.left.left.left = new Node(2, 126);
        root.right.left.left.left.right = new Node(1, 151);
        root.right.left.left.right = new Node(10, 165);
        root.right.left.left.right.right = new Node(12, 167);

        System.out.println("Clan 8 : 116 Red");
        System.out.println("├── Clan 11 : 177 Red");
        System.out.println("│   ├── Clan 7 : 181 Red");
        System.out.println("│   └── Clan 5 : 168 Blue");
        System.out.println("│       └─Clan 6 : 161 Red");
        System.out.println("│           ├─ Clan 10 : 165 Blue");
        System.out.println("│           │  ├─ Clan 12 : 167 Blue");
        System.out.println("│           └── Clan 9 : 136 Blue");
        System.out.println("│               ├─ Clan 1 : 151 Blue");
        System.out.println("│               ├─ Clan 2 : 126 Blue");
        System.out.println("├── Clan 4 : 89 Blue");
        System.out.println("    |─ Clan 3 : 109 Blue\n");

        System.out.println("All Clan (Pre Order)");
        PohonClan.preOrder(root);
        System.out.println();

        System.out.println("All Clan (In Order)");
        PohonClan.inOrder(root);
        System.out.println();

        System.out.println("All Clan (Post Order)");
        PohonClan.postOrder(root);
        System.out.println();

        System.out.println("All Clan (Level Order)");
        PohonClan.levelOrder(root);
        System.out.println();

        System.out.println("Clan 8 : 116 Red");
        System.out.println("├── Clan 11 : 177 Red");
        System.out.println("│   ├── Clan 7 : 181 Red");
        System.out.println("│   └── Clan 5 : 168 Blue");
        System.out.println("│       └── Clan 10 : 165 Red");
        System.out.println("│           ├── Clan 12 : 167 Blue");
        System.out.println("│           └── Clan 1 : 151 Blue");
        System.out.println("│               └── Clan 2 : 126 Blue");
        System.out.println("├── Clan 4 : 89 Blue");
        System.out.println("    |─ Clan 3 : 109 Blue\n");

        PohonClan.printClanDepth(root, 2);
        PohonClan.printClanDepth(root, 5);

        System.out.println("List Member Tiap Clan\n");
        System.out.println("Clan 7");
        System.out.println("Yamato, Atk: 95, Energy: 90");
        System.out.println("Hiroshi, Atk: 85, Energy: 80");
        System.out.println("Takashi, Atk: 100, Energy: 95\n");
        System.out.println("Clan 6");
        System.out.println("Hideo, Atk: 92, Energy: 90");
        System.out.println("Akira, Atk: 80, Energy: 75");
        System.out.println("Daichi, Atk : 78, Energy: 70\n");
        System.out.println("Clan 8");
        System.out.println("Rina, Atk: 65, Energy: 60");
        System.out.println("Sakura, Atk: 60, Energy: 55");
        System.out.println("Yumi, Atk: 58, Energy: 50\n");
        System.out.println("Clan 4");
        System.out.println("Goro, Atk: 50, Energy: 45");
        System.out.println("Ichiro, Atk: 48, Energy: 40");
        System.out.println("Jiro, Atk: 45, Energy: 40\n");
        System.out.println("Clan 5");
        System.out.println("Kenta, Atk: 95, Energy: 90");
        System.out.println("Yuto, Atk: 85, Energy: 80");
        System.out.println("Kazu, Atk: 80, Energy: 75\n");
        System.out.println("Clan 12");
        System.out.println("Masa, Atk: 98, Energy: 95");
        System.out.println("Sho, Atk: 85, Energy: 80");
        System.out.println("Riku, Atk: 75, Energy: 70\n");
        System.out.println("Clan 11");
        System.out.println("Kenji, Atk: 98, Energy: 95");
        System.out.println("Taro, Atk: 88, Energy: 85");
        System.out.println("Yuki, Atk: 85, Energy: 80\n");
        System.out.println("Clan 9");
        System.out.println("Haruto, Atk: 70, Energy: 65");
        System.out.println("Kaito, Atk: 68, Energy: 60");
        System.out.println("Sora, Atk: 75, Energy: 70\n");
        System.out.println("Clan 3");
        System.out.println("Botan, Atk: 55, Energy: 50");
        System.out.println("Kenta, Atk: 60, Energy: 55");
        System.out.println("Fumio, Atk: 58, Energy: 50\n");
        System.out.println("Clan 1");
        System.out.println("Hiro, Atk: 90, Energy: 85");
        System.out.println("Ken, Atk: 70, Energy: 65");
        System.out.println("Ryo, Atk: 75, Energy: 70\n");
        System.out.println("Clan 10");
        System.out.println("Shiro, Atk: 95, Energy: 90");
        System.out.println("Ren, Atk: 90, Energy: 85");
        System.out.println("Toshi, Atk: 70, Energy: 65\n");
        System.out.println("Clan 2");
        System.out.println("Kaito, Atk: 80, Energy: 45");
        System.out.println("Daichi, Atk: 78, Energy: 70");
        System.out.println("Botan, Atk: 55, Energy: 80\n");
    }
}

